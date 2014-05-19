<?php

namespace Shopify\Resource;

use Shopify\Resource;
use Zend\Http\Request;

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
			$pageNumber = 1;
			if ($i > 0)
			{
				$pageNumber = (($i/self::MAX_RESULTS)+1);
			}
			$products = array_merge($products, $this->_getClient()->request($url . '&page=' . $pageNumber)->products);
		}
		
		return $products;
	}
	
	public function getAllCount()
	{
		return $this->_getClient()->request('/admin/products/count.json')->count;
	}
	
	public function getProductMetafields($id = null)
	{
		if(!is_int($id))
		{
			throw new \InvalidArgumentException('Integer expected, got '. gettype($id));
		}
		
		return $this->_getClient()->request("/admin/products/{$id}/metafields.json")->metafields;
	}
	
	public function getProductVariantMetafields($id = null)
	{
		if(!is_int($id))
		{
			throw new \InvalidArgumentException('Integer expected, got '. gettype($id));
		}
		
		return $this->_getClient()->request("/admin/variants/{$id}/metafields.json")->metafields;
	}
	
	public function createNewMetaFieldForProductVariant($productVariantId = null, $metaFieldNamespace = NULL, $metaFieldName = NULL, $metaFieldValue = NULL)
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

    	return $this->_getClient()->request(
			"/admin/variants/{$productVariantId}/metafields.json", 
			Request::METHOD_POST, 
			array(
				'metafield' => array(
					'namespace' => $metaFieldNamespace,
					'key' => $metaFieldName,
					'value' => $metaFieldValue,
					'value_type' => (is_int($metaFieldValue) ? 'integer' : 'string')
				)
    		)
    	);
    }
	
	public function createNewMetaFieldForProduct($productId = null, $metaFieldNamespace = NULL, $metaFieldName = NULL, $metaFieldValue = NULL)
	{
    	// Sanity Checking
    	if (is_null($productId))
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

    	return $this->_getClient()->request(
			"/admin/products/{$productId}/metafields.json", 
			Request::METHOD_POST, 
			array(
				'metafield' => array(
					'namespace' => $metaFieldNamespace,
					'key' => $metaFieldName,
					'value' => $metaFieldValue,
					'value_type' => (is_int($metaFieldValue) ? 'integer' : 'string')
				)
    		)
    	);
    }
	
	public function getAllMetafields($options = null)
	{
		$response = $this->_getClient()->request('/admin/metafields.json');
		
		return $response;
	}
    
    /**
     * Edit Stock Quantity
     * @param integer $productId
     * @param integer $qty
     */
    public function editStockQuantity($productVariantId = NULL, $qty = 0)
    {
    	// Sanity Checking
    	if (is_null($productVariantId))
    	{
    		throw new \InvalidArgumentException('Variant Id must be specified');
    	}
    	    	
    	return $this->updateProductVariant($productVariantId, array(
    		'variant' => array(
		    	'id' => $productVariantId,
		    	'inventory_quantity' => $qty
		    )
    	));
    }
    
    public function editVariantWasPrice($productVariantId = NULL, $variantWasPrice = 0)
    {
    	// Sanity Checking
    	if (is_null($productVariantId))
    	{
    		throw new \InvalidArgumentException('Variant Id must be specified');
    	}
    	
    	if (!$variantWasPrice)
    	{
    		throw new \InvalidArgumentException('Was Price must be greater than zero');
    	}
    	
    	return $this->updateProductVariant($productVariantId, array(
    		'variant' => array(
		    	'id' => $productVariantId,
		    	'compare_at_price' => $variantWasPrice
		    )
    	));
    }
    
    public function editVariantPrice($productVariantId = NULL, $variantPrice = 0)
    {
    	// Sanity Checking
    	if (is_null($productVariantId))
    	{
    		throw new \InvalidArgumentException('Variant Id must be specified');
    	}
    	
    	if (!$variantPrice)
    	{
    		throw new \InvalidArgumentException('Price must be greater than zero');
    	}
    	
    	return $this->updateProductVariant($productVariantId, array(
    		'variant' => array(
		    	'id' => $productVariantId,
		    	'price' => $variantPrice
		    )
    	));
    }
    
    /**
     * Update Meta Field value of existing Meta Field
     * @param integer $metaFieldId
     * @param string|integer $value
     * @throws \InvalidArgumentException
     * @return Ambigous <string, mixed>
     */
    public function updateMetaField($metaFieldId, $value)
    {
    	// Sanity Checking
    	if (!is_int($metaFieldId))
    	{
    		throw new \InvalidArgumentException('Integer expected, got '. gettype($metaFieldId));
    	}
    	if (!is_string($value))
    	{
    		throw new \InvalidArgumentException('Integer expected, got '. gettype($value));
    	}
    	
    	return $this->_getClient()->request(
    			"/admin/metafields/{$metaFieldId}.json",
    			Request::METHOD_PUT,
    			array(
    				'metafield' => array(
    					'id' => $metaFieldId,
    					'value' => $value
    				)
    			)
    	);
    }
    
    /**
     * Core method to update the product variant
     * @param integer $productVariantId
     * @param array $data
     * @return Ambigous <string, mixed>
     */
    public function updateProductVariant($productVariantId, $data)
    {
    	return $this->_getClient()->request(
    			"/admin/variants/{$productVariantId}.json",
    			Request::METHOD_PUT,
    			$data
    	);
    }
    
    /**
     * Core method to update the product  
     * @param integer $productId
     * @param array $data
     */
    public function updateProduct($productId, $data)
    {
    	return $this->_getClient()->request(
    			"/admin/products/{$productId}.json",
    			Request::METHOD_PUT,
    			$data
    	);
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