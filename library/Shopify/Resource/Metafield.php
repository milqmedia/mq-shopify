<?php
namespace Shopify\Resource;

use Shopify\Resource;
class Metafield extends Resource
{
	const MAX_RESULTS = 250;
	
	public function __construct($options)
	{
		parent::__construct($options);
	
		$this->setConfig(array(
				'url' => '/admin/products.json'
		));
	}
	
	/**
	 * Get All Metafields for Store
	 * @see \Shopify\Resource::get()
	 */
	public function get($options = NULL)
	{
		return $this->_getClient()->request("/admin/metafields.json");
	}
	
	/**
	 * Get all metafields from product id
	 * @param integer $productId
	 */
	public function getMetafieldsOnProduct($productId)
	{
		if(!is_int($productId))
		{
			throw new \InvalidArgumentException('Integer expected, got '. gettype($productId));
		}
		
		return $this->_getClient()->request("/admin/products/{$productId}/metafields.json");
	}
}