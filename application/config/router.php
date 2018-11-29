<?php

$router = $di->getRouter();

$router->add('/(backend)/?([\w]*)/?([\w]*)(/.*)*',['module'=>1,'controller' => 2,'action' => 3,'params'=>4]);
// $router->add('/(frontend)/?([\w]*)/?([\w]*)(/.*)*',['module'=>1,'controller' => 2,'action' => 3,'params'=>4]);

$router->setDefaultModule("frontend");

$router->handle();
