<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
// $routes->get('/', 'Home::index');
$routes->get('/', 'Main::index');
$routes->get('/healthcheck', 'Main::healthcheck');
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
$routes->match(['get', 'post'],'/notificationUser/add', 'DependentTables::notificationsUsersAdd');
$routes->match(['get'],'/notificationsUsers', 'DependentTables::getNotificationsUsers');
$routes->match(['get', 'post'],'/notificationUser/add', 'DependentTables::notificationsUsersAdd');
$routes->match(['get'],'/notificationsUsers', 'DependentTables::getNotificationsUsers');
$routes->match(['get', 'post'],'/dispatcherConfirm/add', 'DependentTables::dispatcherConfirmsAdd');
$routes->match(['get'],'/dispatcherConfirms', 'DependentTables::getDispatcherConfirms');
$routes->match(['get', 'post'],'/dispatcherSupportQuestion/add', 'DependentTables::dispatcherSupportQuestionsAdd');
$routes->match(['get'],'/dispatcherSupportQuestions', 'DependentTables::getDispatcherSupportQuestions');
$routes->match(['get', 'post'],'/dispatcherSupportAnswer/add', 'DependentTables::dispatcherSupportAnswersAdd');
$routes->match(['get'],'/dispatcherSupportAnswers', 'DependentTables::getDispatcherSupportAnswers');
$routes->match(['get', 'post'],'/supportDeveloperQuestion/add', 'DependentTables::supportDeveloperQuestionsAdd');
$routes->match(['get'],'/supportDeveloperQuestions', 'DependentTables::getSupportDeveloperQuestions');
$routes->match(['get', 'post'],'/supportDeveloperAnswer/add', 'DependentTables::supportDeveloperAnswersAdd');
$routes->match(['get'],'/supportDeveloperAnswers', 'DependentTables::getSupportDeveloperAnswers');

$routes->match(['get'],'/supportDeveloperAnswers', 'DependentTables::getSupportDeveloperAnswers');


$routes->match(['get'],'/fill-examples_data', 'ExampleFill::fill');
$routes->match(['get'],'/truncate_tables', 'ManageTables::truncate');
$routes->match(['get'],'/create_tables', 'ManageTables::create');
