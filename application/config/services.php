<?php 
use Phalcon\Mvc\View;

/**
 * Shared configuration service
 */
$di->setShared('config', function () {
    return include APP_PATH . "/config/config.php";
});

$config = $di->getConfig();

/**
 * Setting up the view component
 */
$di->set(
    "view",
    function () use ($config) {
        $view = new View();

        // A trailing directory separator is required
        $view->setViewsDir($config->application->viewsDir.'/'.$this->get('router')->getModuleName());
        $view->setLayoutsDir('layouts/');
        $view->setTemplateAfter('index');
        
//         $view->registerEngines([".html"   => "Phalcon\\Mvc\\View\\Engine\\Php"]);
        return $view;
    },
    true
);

/**
 * Database connection is created based in the parameters defined in the configuration file
 */
//主库
$di->setShared('dbMaster', function () {
    $config = $this->getConfig();

    $class = 'Phalcon\Db\Adapter\Pdo\\' . $config->database->master->adapter;
    $params = $config->database->master->toArray();

    if ($config->database->master->adapter == 'Postgresql') {
        unset($params['charset']);
    }

    $connection = new $class($params);

    return $connection;
});