<?php

$loader = new \Phalcon\Loader();

/**
 * We're a registering a set of directories taken from the configuration file
 */
$moduleNamespaces = [
        'Basic'                      => APP_PATH.'/basic',
        'Library'                    => APP_PATH.'/library',
        'Logics'                     => APP_PATH.'/logics',
        'Models'                     => APP_PATH.'/models'
    ];

$loader->registerNamespaces($moduleNamespaces);
$loader->register();