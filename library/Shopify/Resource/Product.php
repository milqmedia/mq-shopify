<?php

namespace Shopify\Resource;

use Shopify\Resource;

class Product extends Resource
{
	
	const MAX_RESULTS = 250;
	
	public function __construct($options)
	{
		parent::__construct($options);
		
		$this->setConfig(array(
			'url' => '/admin/products.json'
		));
	}
	
	public function get($id = null)
	{
		if(!is_int($id))
		{
			throw new \InvalidArgumentException('Integer expected, got '. gettype($id));
		}
			
		return $this->_getClient()->request('/admin/products/{$id}.json');
	}
	
	public function getAll($options = null)
	{
		$products = array();
		
		$url = '/admin/products.json?limit='. self::MAX_RESULTS;
		
		if(is_array($options))
		{
			$url .= '&'. $this->_flattenOptions($options);
		}
		
		for($i=0; $i <= $this->getAllCount(); $i += self::MAX_RESULTS)
		{
			$products = array_merge($products, $this->_getClient()->request($url)->products);
		}
		
		return $products;
	}
	
	public function getAllCount()
	{
		return $this->_getClient()->request('/admin/products/count.json')->count;
	}
	
	public function getMetafields($id = null)
	{
		if(!is_int($id))
		{
			throw new \InvalidArgumentException('Integer expected, got '. gettype($id));
		}
		
		return $this->_getClient()->request("/admin/products/{$id}/metafields.json")->metafields;
	}
	
	public function getAllMetafields($options = null)
	{
		$response = $this->_getClient()->request('/admin/metafields.json');
		
		return $response;
	}
	
	public function update($product = null)
	{
		if(!is_object($product))
		{
			throw new \InvalidArgumentException('Object expected, got '. gettype($id));
		}
		
		throw new \Exception('Not implemented yet.');
		return $product;
	}
}