<?php
namespace Shopify\Resource;

use Shopify\Resource;
use Zend\Http\Request;
class Order extends Resource
{
	public function __construct($options)
	{
		parent::__construct($options);
	}
	
	/**
	 * Recieve a single order
	 * @see \Shopify\Resource::get()
	 */
	public function get($id = null)
	{
		if(!is_int($id))
		{
			throw new \InvalidArgumentException('Integer expected, got '. gettype($id));
		}
			
		return $this->_getClient()->request("/admin/orders/{$id}.json");
	}
	
	
	/**
	 * Fulfil Whole Orders
	 * Note: this does not take a parameter for fulfiilling part of an order
	 * @param integer $orderId
	 * @param string $trackingCompany
	 * @param string $trackingUrl
	 * @link http://docs.shopify.com/api/fulfillment#create
	 */
	public function fulfillWholeOrder($orderId, $trackingCompany = NULL, $trackingNumber = NULL, $trackingUrl = NULL)
	{
		return $this->_getClient()->request(
				"/admin/orders/{$orderId}/fulfillments.json",
				Request::METHOD_POST,
				array(
					'fulfillment' => array(
						'tracking_company' => $trackingCompany,
						'tracking_number' => $trackingNumber,
						'tracking_url' => $trackingUrl
					)
				)
		);
	}
	
	public function fulfillPartOrder($orderId, $orderLineIds,  $trackingCompany = NULL, $trackingNumber = NULL, $trackingUrl = NULL)
	{
		return $this->_getClient()->request(
				"/admin/orders/{$orderId}/fulfillments.json",
				Request::METHOD_POST,
				array(
						'fulfillment' => array(
								'tracking_company' => $trackingCompany,
								'tracking_number' => $trackingNumber,
								'tracking_url' => $trackingUrl,
								'line_items' => $orderLineIds
						)
				)
		);
	}
}