<?php

use App\Core\Router;

Router::get('/', 'PageController@home');

Router::get('/user/login', 'UserController@login');
Router::post('/user/loginValidate', 'UserController@loginValidate');
Router::get('/user/logout', 'UserController@logout');

Router::group('/admin', 'AdminAuthMiddleware', function () {
  Router::get('', 'Admin\\AdminController@home');

  Router::get('/product', 'Admin\\AppProductController@product');
  Router::post('/product/table', 'Admin\\AppProductController@table');
  Router::post('/product/import', 'Admin\\AppProductController@import');
  Router::get('/product/export', 'Admin\\AppProductController@export');

  Router::get('/product/form', 'Admin\\AppProductController@form');
  Router::get('/product/form/:id', 'Admin\\AppProductController@formEdit');

  Router::post('/product/create', 'Admin\\AppProductController@create');
  Router::post('/product/update', 'Admin\\AppProductController@update');

  Router::get('/product/validate', 'Admin\\AppProductController@validate');
  Router::post('/product/verified', 'Admin\\AppProductController@verified');
});
