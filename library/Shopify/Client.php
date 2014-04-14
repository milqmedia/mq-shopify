<?php 

namespace Shopify;

class Client
{
	const USER_AGENT = 'PHP Shopify API v0.0.1';
	
	private $_client = null;
	
	private $_config = array(
		'api_key' => '',
		'secret'  => '',
		'shop'    => ''
	);
	
	private $_url = '';
	
	public function __construct($config = null)
	{
		if ($config !== null) {
			$this->setConfig($config);
		}
	}
	
	public function request($request = null)
	{
		if(is_null($request))
		{
			throw new \InvalidArgumentException('You must specify a request url.');
		}
		elseif(!is_string($request))
		{
			throw new \InvalidArgumentException('String expected, got ' . gettype($request));
		}
		
		$urlParts = parse_url($this->_getUrl());
		
		$req = new \Zend\Http\Request();
		$req->setUri($this->_getUrl() . $request);
		$this->_client->setAuth($urlParts['user'], $urlParts['pass']);
		
		$response = $client->dispatch($req);
		if ($response->isSuccess()) {
			//  the POST was successful
			return json_decode($response->getBody());
		}
	}
	
	private function _getClient()
	{
		if(!is_object($this->_client) || !$this->_client instanceof \Zend\Http\Client)
		{
			$adapter = new \Zend\Http\Client\Adapter\Curl();
			
			$this->_client = new \Zend\Http\Client();
			$this->_client->setAdapter($adapter);
		}
		
		return $this->_client;
	}
	
	private function _getUrl()
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
