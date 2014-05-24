<?php 

namespace Shopify;

use Zend\Http\Request;
use Zend\Stdlib\Parameters;
class Client
{
    /**
     * USER_AGENT
     * @var string
     */
    const USER_AGENT = 'PHP Shopify API v0.0.1';
    
    /**
     * The HTTP Client
     * @var \Zend\Http\Client
     */
    private $_httpClient = null;
    
    /**
     * Configuration array
     * @var array
     */
    private $_config = array(
        'api_key' => '',
        'secret'  => '',
        'shop'    => ''
    );
    
    /**
     * FQDN URL for the shop
     * @var string
     */
    private $_url = '';
    
    /**
     * Constructor
     * @param array|NULL $config
     */
    public function __construct($config = null)
    {
        if ($config !== null) {
            $this->setConfig($config);
        }
    }
    
    /**
     * Send a GET Request
     * @param string $request
     * @throws \InvalidArgumentException
     * @todo Handle when the $response is not successful
     * @return string
     */
    public function request($request = null, $method = null, $data = null)
    {
    	// Sanity checking
        if(is_null($request))
        {
            throw new \InvalidArgumentException('You must specify a request url.');
        }
        elseif(!is_string($request))
        {
            throw new \InvalidArgumentException('String expected, got ' . gettype($request));
        }
        
        // Spit the domain name into an array so we can extract the user and pass
        $urlParts = parse_url($this->getUrl());
        
        // Send the request 
        $req = new \Zend\Http\Request();
        $req->setUri($this->getUrl() . $request);
        $req->getHeaders()->addHeaders(array(
        	'Content-Type' => 'application/x-www-form-urlencoded; charset=UTF-8'
        ));
        if (!is_null($method))
        {
        	$req->setMethod($method);
        }
        else 
        {
        	$req->setMethod(Request::METHOD_GET);
        }
        if (!is_null($data))
        {
        	$req->setPost(new Parameters($data));
        }
        
        $this->_getHttpClient()->setAuth($urlParts['user'], $urlParts['pass']);
        
        // Get the response
        $response = $this->_getHttpClient()->dispatch($req);
        
        // Check the Rate Limit
        // If we're hitting 3/4 of the allowed limit sleep 1 sec
        // @link http://docs.shopify.com/api/tutorials/learning-to-respect-the-api-call-limit
        $headers = $response->getHeaders();
        $rawLimit = $headers->get('HTTP_X_SHOPIFY_SHOP_API_CALL_LIMIT')->getFieldValue();
        list ($currentRate, $currentLimit) = split('/');
        if ($currentRate > ($currentLimit - ($currentLimit/0.25)))
        {
        	sleep(1);
        }
        
        // If the response was successful
        if ($response->isSuccess()) {
            return json_decode($response->getBody());
        }
        else 
        // Try again 
        {
        	$this->request($request,$method,$data);
        }
    }
    
    /**
     * Get HTTP Client
     * If not set, the cUrl adapter is used to prevent issues with 
     * connecting to https
     * 
     * @return \Zend\Http\Client
     */
    private function _getHttpClient()
    {
        if(!is_object($this->_httpClient) || !$this->_httpClient instanceof \Zend\Http\Client)
        {
        	// Set the adapter to cUrl
            $adapter = new \Zend\Http\Client\Adapter\Curl();
            
            $this->_httpClient = new \Zend\Http\Client();
            $this->_httpClient->setAdapter($adapter);
        }
        
        return $this->_httpClient;
    }
    
    /**
     * Builds the URL so the API can connect
     * @throws \Exception
     * @return string
     */
    public function getUrl()
    {
        if(!isset($this->_config['api_key']) || $this->_config['api_key'] == '')
        {
            throw new \Exception('Api key not set!');
        }
        
        if(!isset($this->_config['secret']) || $this->_config['secret'] == '')
        {
            throw new \Exception('Secret not set!');
        }
        
        if(!isset($this->_config['shop']) || $this->_config['shop'] == '')
        {
            throw new \Exception('Shop not set!');
        }
        
        return 'https://'. $this->_config['api_key'] .':'. $this->_config['secret'] .'@'. $this->_config['shop'];
    }
    
    /**
     * Set the config
     * @param array $config
     * @throws \InvalidArgumentException
     * 
     * @return \Shopify\Client
     */
    public function setConfig($config = array())
    {
        if ($config instanceof \Zend\Config\Config) {
            $config = $config->toArray();

        } elseif(!is_array($config)) {
            throw new \InvalidArgumentException('Array or Zend_Config object expected, got ' . gettype($config));
        }

        foreach($config as $k => $v) {
            $this->_config[strtolower($k)] = $v;
        }

        return $this;
    }
}
