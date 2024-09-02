<?php

$router->get('/', 'HomeController@index');
$router->get('/listings', 'ListingController@index');
$router->get('/listings/create', 'ListingController@create', ['auth']);
$router->get('/listings/{id}', 'ListingController@show');
$router->get('/listings/edit/{id}', 'ListingController@edit', ['auth']);
$router->post('/listings', 'ListingController@store', ['auth']);
$router->post('/listings/update/{id}', 'ListingController@update', ['auth']);
$router->post('/listings/delete/{id}', 'ListingController@destroy', ['auth']);

$router->get('/auth/register', 'UserController@create', ['guest']);
$router->post('/auth/register', 'UserController@store', ['guest']);
$router->get('/auth/login', 'UserController@login', ['guest']);
$router->post('/auth/logout', 'UserController@logout', ['auth']);
$router->post('/auth/login', 'UserController@authenticate', ['guest']);
