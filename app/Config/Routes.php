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
$routes->match(['get', 'post'],'/criticality/add', 'IndependentTables::addCriticality');
$routes->match(['get'],'/criticality', 'IndependentTables::getСriticality');
$routes->match(['get', 'post'],'/reason/add', 'IndependentTables::addReason');
$routes->match(['get'],'/reasons', 'IndependentTables::getReasons');
$routes->match(['get', 'post'],'/dispatcherStatus/add', 'IndependentTables::addDispatcherStatus');
$routes->match(['get'],'/dispatcherStatuses', 'IndependentTables::getDispatcherStatuses');
$routes->match(['get', 'post'],'/malfunction/add', 'DependentTables::malfunctionsAdd');
$routes->match(['get'],'/malfunctions', 'DependentTables::getMalfunctions');
$routes->match(['get', 'post'],'/notification/add', 'DependentTables::notificationsAdd');
$routes->match(['get'],'/notifications', 'DependentTables::getNotifications');


$routes->match(['get'],'/template/malfunctions', 'TemplateTables::getMalfunctions');
