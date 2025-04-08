<?php

require __DIR__ . '/router.php';

Route::add('/', function() {
    require __DIR__ . '/login.php';
});

Route::add('/dashboard', function() {
    require __DIR__ . '/dashboard.php';
});

Route::add('/product', function() {
    require __DIR__ . '/product.php';
});

Route::add('/addproduct', function() {
    require __DIR__ . '/add_new_product.php';
});


Route::add('/report/{id}', function($id) {
    $order_id = htmlspecialchars($id);
    require __DIR__ . '/report/index.php';
});

Route::add('/viewOrder/{id}', function($id) {
    $order_id = htmlspecialchars($id);
    require __DIR__ . '/view_order.php';
});

Route::add('/order/{id}', function($id) {
    $order_id = htmlspecialchars($id);
    require __DIR__ . '/order_process_page.php';
});

Route::add('/addOrder', function() {
    require __DIR__ . '/add_new_order.php';
});


Route::add('/login', function() {
    require __DIR__ . '/views/login.php';
});

Route::add('/register', function() {
    require __DIR__ . '/views/register.php';
});

Route::add('/logout', function() {
    require __DIR__ . '/views/logout.php';
});


$uri = explode('?', $_SERVER['REQUEST_URI'])[0];

Route::submit();