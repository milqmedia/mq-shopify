<?php

namespace Shopify;

abstract class Resource
{
	protected $_config = array(
		'client' => null,
		'url'    => ''
	);
	
	public function __construct($config = null)
	{
		if ($config !== null) {
			$this->setConfig($config);
		}
	}
	
	public function setConfig($config = array())
	{
        if($config instanceof \Zend\Config\Config) {
            $config = $config->toArray();

        } elseif(!is_array($config)) {
            throw new \InvalidArgumentException('Array or Zend_Config object expected, got '. gettype($config));
        }

        foreach($config as $k => $v) {
            $this->_config[strtolower($k)] = $v;
        }

        return $this;
    }
    
    protected function _getClient()
    {
    	if(!$this->_config['client'] instanceof Client)
    		throw new \Exception('Shopify\Client object expected, got '. gettype($this->_config['client']));
    		
    	return $this->_config['client'];
    }
    
    protected function _getUrl()
    {
    	if($this->_config['url'] == '')
    		throw new \Exception('Url is empty.');
    		
    	return $this->_config['url'];
    }
    
    protected function _flattenOptions($options = null)
    {
    	if(!is_array($options))
    	{
    		throw new \InvalidArgumentException('Array expected, got '. gettype($options));
    	}
    	
		$_options = array();
		
		foreach($options as $option => $value)
		{
			if(is_array($value))
			{
				$value = implode(',', $value);
			}
			
			$_options[] = $option .'='. $value;
		}
		
		return implode('&', $_options);
    }
    
    abstract public function get($options = null);
}