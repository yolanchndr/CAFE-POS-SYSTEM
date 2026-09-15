<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Auth\Login::index');

// Authentication Routes
$routes->get('/login', 'Auth\Login::index');
$routes->post('/login/process', 'Auth\Login::process');
$routes->get('/logout', 'Auth\Login::logout');

// Public Monitor Displays
$routes->get('/customer-display', 'Display\CustomerDisplay::index');
$routes->get('/api/customer-display/active-order', 'Display\CustomerDisplay::getActiveOrder');

$routes->get('/queue-display', 'Display\QueueDisplay::index');
$routes->get('/api/queue-display/data', 'Display\QueueDisplay::getQueueData');

// Protected Admin Routes Group
$routes->group('admin', ['filter' => 'auth'], static function ($routes) {
    $routes->get('dashboard', 'Admin\Dashboard::index');
    $routes->get('reports', 'Admin\Dashboard::reports');

    // POS & Payment Routes
    $routes->get('pos', 'Admin\Pos::index');
    $routes->get('pos/product-details/(:num)', 'Admin\Pos::getProductDetails/$1');
    $routes->post('pos/checkout', 'Admin\Pos::checkout');
    $routes->post('pos/process-payment', 'Admin\Pos::processPayment');

    // Orders Management & Print Receipt
    $routes->get('orders', 'Admin\Orders::index');
    $routes->post('orders/update-status/(:num)', 'Admin\Orders::updateStatus/$1');
    $routes->get('orders/print/(:num)', 'Admin\Orders::printReceipt/$1');

    // System Settings
    $routes->get('settings', 'Admin\Settings::index');
    $routes->post('settings/update', 'Admin\Settings::update');

    // Categories CRUD
    $routes->get('categories', 'Admin\Categories::index');
    $routes->post('categories/store', 'Admin\Categories::store');
    $routes->post('categories/update/(:num)', 'Admin\Categories::update/$1');
    $routes->get('categories/delete/(:num)', 'Admin\Categories::delete/$1');

    // Products CRUD & Status
    $routes->get('products', 'Admin\Products::index');
    $routes->post('products/store', 'Admin\Products::store');
    $routes->post('products/update/(:num)', 'Admin\Products::update/$1');
    $routes->get('products/toggle-availability/(:num)', 'Admin\Products::toggleAvailability/$1');
    $routes->get('products/delete/(:num)', 'Admin\Products::delete/$1');

    // Variants CRUD
    $routes->get('variants', 'Admin\Variants::index');
    $routes->post('variants/store', 'Admin\Variants::store');
    $routes->post('variants/update/(:num)', 'Admin\Variants::update/$1');
    $routes->get('variants/delete/(:num)', 'Admin\Variants::delete/$1');

    // Toppings CRUD
    $routes->get('toppings', 'Admin\Toppings::index');
    $routes->post('toppings/store', 'Admin\Toppings::store');
    $routes->post('toppings/update/(:num)', 'Admin\Toppings::update/$1');
    $routes->get('toppings/delete/(:num)', 'Admin\Toppings::delete/$1');

    // Tables CRUD
    $routes->get('tables', 'Admin\Tables::index');
    $routes->post('tables/store', 'Admin\Tables::store');
    $routes->post('tables/update/(:num)', 'Admin\Tables::update/$1');
    $routes->get('tables/delete/(:num)', 'Admin\Tables::delete/$1');
});


$routes->group('admin', ['filter' => 'auth'], static function ($routes) {
    // ... route admin lainnya ...

    // Payments Route
    $routes->get('payments', 'Admin\Payments::index');

    // ...
});

$routes->group('admin', ['filter' => 'auth'], static function ($routes) {
    // ... route admin lainnya ...

    // Activity Logs Route
    $routes->get('activity-logs', 'Admin\ActivityLogs::index');

    // ...
});