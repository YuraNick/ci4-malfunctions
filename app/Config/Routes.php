<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
// $routes->get('/', 'Home::index');
$routes->get('/', 'Main::index');
$routes->match(['get', 'post'],'/user/add', 'IndependentTables::addUser');
$routes->match(['get'],'/users', 'IndependentTables::getUsers');
$routes->match(['get', 'post'],'/monObject/add', 'IndependentTables::addMonObject');
$routes->match(['get'],'/monObjects', 'IndependentTables::getMonObjects');
