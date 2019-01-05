<?php
use Phalcon\Mvc\Application;
use Phalcon\Di\FactoryDefault;
use Library\Log;
use Phalcon\Logger;

error_reporting(E_ALL);

define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/application');
define('APP_NAME', 'wxgroup');
date_default_timezone_set('Asia/Shanghai');

require_once BASE_PATH.'/vendor/autoload.php';

$di = new FactoryDefault();

/**
 * Handle routes
 */
include APP_PATH . '/config/router.php';

/**
 * Read services
 */
include APP_PATH . '/config/services.php';

/**
 * Include Autoloader
 */
include APP_PATH . '/config/loader.php';

Log::setTriggerError();


// 创建应用
$application = new Application($di);

// 注册模块
$application->registerModules($config->application->modules->toArray());
try{
    // 处理请求
    $response = $application->handle();

    $response->send();
} catch (\Exception $e) {
    Log::write('system', $e->getMessage());
}