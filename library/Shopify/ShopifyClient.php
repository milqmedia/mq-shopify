<?php 

namespace Shopify;

use Zend\Config\Config,
	Zend\Http\Client;

class ShopifyClient
{
	private $_client = null;
	
	private $_config = array(
		'api_key' => '',
		'secret'  => '',
		'shop'    => '',
		'token'   => ''
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
		
		$this->_getClient()->setUri($this->_getUrl() . $request);
		
		return $this->_getClient()->request();
	}
	
	private function _getClient()
	{
		if(!is_object($this->_client) || !$this->_client instanceof Client)
		{
			$this->_client = new Client();
		}
	}
	
	private function _getUrl()
	{
		return 'https://'. $this->_config['api_key'] .':'. $this->_config['secret'] .'@'. $this->_config['shop'] .'/admin/';
	}
	
	public function setConfig($config = array())
    {
        if ($config instanceof Config) {
            $config = $config->toArray();

        } elseif(!is_array($config)) {
            throw new \InvalidArgumentException('Array or Zend_Config object expected, got ' . gettype($config));
        }

        foreach($config as $k => $v) {
            $this->config[strtolower($k)] = $v;
        }

        return $this;
    }
}