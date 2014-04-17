<?php

namespace Shopify\Resource;

use Shopify\Resource;
use Zend\Http\Request;
use Zend\Stdlib\Parameters;

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
	
	public function createNewMetaField($productVariantId = null, $metaFieldNamespace = NULL, $metaFieldName = NULL, $metaFieldValue = NULL)
	{
    	// Sanity Checking
    	if (is_null($productVariantId))
    	{
    		throw new \InvalidArgumentException('Variant Id must be specified');
    	}
    	if (is_null($metaFieldNamespace))
    	{
    		throw new \InvalidArgumentException('Namespace must be specified');
    	}
    	if (is_null($metaFieldName))
    	{
    		throw new \InvalidArgumentException('Field Name must be specified');
    	}
    	if (is_null($metaFieldValue))
    	{
    		throw new \InvalidArgumentException('Field Value must be specified');
    	}
    	    	
    	// Send the request
    	$client = new \Zend\Http\Client();
    	$adapter = new \Zend\Http\Client\Adapter\Curl();
        $client->setAdapter($adapter);
    	
    	$req = new \Zend\Http\Request();
    	$req->setUri($this->_getClient()->getUrl() . '/admin/products/' . $productVariantId . '/metafields.json');
    	$req->getHeaders()->addHeaders(array(
    		'Content-Type' => 'application/x-www-form-urlencoded; charset=UTF-8'
    	));
    	$req->setMethod(Request::METHOD_POST);
    	$req->setPost(new Parameters(array(
    		'metafield' => array(
    			'namespace' => $metaFieldNamespace,
    			'key' => $metaFieldName,
    			'value' => $metaFieldValue
    		)
    	)));
    	// Get the response
    	$response = $client->dispatch($req);
    	
    	// If the response was successful
    	if ($response->isSuccess()) {
    		return json_decode($response->getBody());
    	}
    }
	
	public function getAllMetafields($options = null)
	{
		$response = $this->_getClient()->request('/admin/metafields.json');
		
		return $response;
	}
    
    /**
     * Modify an existing product
     * @param integer $productId
     */
    public function editStockQuantity($variantId = NULL, $qty = 0)
    {
    	// Sanity Checking
    	if (is_null($variantId))
    	{
    		throw new \InvalidArgumentException('Variant Id must be specified');
    	}
    	    	
    	// Send the request
    	$client = new \Zend\Http\Client();
    	$adapter = new \Zend\Http\Client\Adapter\Curl();
    	$client->setAdapter($adapter);
    	
    	$req = new \Zend\Http\Request();
    	$req->setUri($this->_getUrl() . '/admin/variants/' . $variantId . '.json');
    	$req->getHeaders()->addHeaders(array(
    		'Content-Type' => 'application/x-www-form-urlencoded; charset=UTF-8'
    	));
    	$req->setMethod(Request::METHOD_PUT);
    	$req->setPost(new Parameters(array(
    		'variant' => array(
    			'id' => $variantId,
    			'inventory_quantity' => $qty
    		)
    	)));
    	// Get the response
    	$response = $client->dispatch($req);
    	
    	// If the response was successful
    	if ($response->isSuccess()) {
    		return json_decode($response->getBody());
    	}
    }
    
    public function updateMetaField($metaFieldId, $value)
    {

    	// Sanity Checking
    	if (is_null($productVariantId))
    	{
    		throw new \InvalidArgumentException('Variant Id must be specified');
    	}
    	if (is_null($metaFieldNamespace))
    	{
    		throw new \InvalidArgumentException('Namespace must be specified');
    	}
    	if (is_null($metaFieldName))
    	{
    		throw new \InvalidArgumentException('Field Name must be specified');
    	}
    	if (is_null($metaFieldValue))
    	{
    		throw new \InvalidArgumentException('Field Value must be specified');
    	}
    	
    	// Send the request
    	$client = new \Zend\Http\Client();
    	$adapter = new \Zend\Http\Client\Adapter\Curl();
    	$client->setAdapter($adapter);
    	
    	$req = new \Zend\Http\Request();
    	$req->setUri($this->_getUrl() . '/admin/metafields/' . $metaFieldId . '.json');
    	$req->getHeaders()->addHeaders(array(
    			'Content-Type' => 'application/x-www-form-urlencoded; charset=UTF-8'
    	));
    	$req->setMethod(Request::METHOD_PUT);
    	$req->setPost(new Parameters(array(
    			'metafield' => array(
    					'id' => $metaFieldId,
    					'value' => $value
    			)
    	)));
    	// Get the response
    	$response = $client->dispatch($req);
    	 
    	// If the response was successful
    	if ($response->isSuccess()) {
    		return json_decode($response->getBody());
    	}
    	
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