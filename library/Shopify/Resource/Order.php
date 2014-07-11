<?php
namespace Shopify\Resource;

use Shopify\Resource;
class Order extends Resource
{
	public function __construct($options)
	{
		parent::__construct($options);
	}
	
	/**
	 * Fulfil Whole Orders
	 * Note: this does not take a parameter for fulfiilling part of an order
	 * @param integer $orderId
	 * @param string $trackingCompany
	 * @param string $trackingUrl
	 * @link http://docs.shopify.com/api/fulfillment#create
	 */
	public function fulfillWholeOrder($orderId, $trackingCompany = NULL, $trackingUrl = NULL)
	{
		return $this->_getClient()->request(
				"/admin/orders/{$orderId}/fulfillments.json",
				Request::METHOD_POST,
				array(
					'fulfillment' => array(
						'tracking_company' => $trackingCompany,
						'tracking_url' => $trackingUrl
					)
				)
		);
	}
}