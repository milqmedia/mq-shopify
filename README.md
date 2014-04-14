PHP-Shopify-API
=============


Basic Example
-----
```php
<?php
        define('API_KEY', 'xxxxx');
        define('SECRET', 'xxxxx');

        require_once('Zend/Loader/StandardAutoloader.php');

        $loader = new \Zend\Loader\StandardAutoloader();
        $loader->registerNamespaces(array(
                'Zend' => __DIR__ .'/library/Zend',
                'Shopify' => __DIR__ .'/library/Shopify'
        ));
        $loader->register();

        $client = new \Shopify\Client(array(
                'shop'    => $_GET['shop'],
                'api_key' => API_KEY,
                'secret'  => SECRET
        ));

        $ShopifyProduct = new \Shopify\Resource\Product(array(
                'client' => $client
        ));

        $products = $ShopifyProduct->getAll(array(
                'fields'=> array('id'),
                'collection_id' => '3782202'
        ));

        print_r($products);
```
