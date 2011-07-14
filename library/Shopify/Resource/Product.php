<?php

namespace Shopify\Resource;

use Shopify\Resource;

class Product extends Resource
{
	public function __construct($options)
	{
		parent::__construct($options);
		
		$this->setConfig(array(
			'url' => '/admin/products.json'
		));
	}
	
	public function get($options = null) {
		
		return $this->_getClient()->request($this->_getUrl());
	}
}