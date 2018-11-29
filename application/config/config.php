<?php
/*
 * Modified: prepend directory path of current file, because of this file own different ENV under between Apache and command line.
 * NOTE: please remove this comment.
 */

return new \Phalcon\Config([
    'application' => [
        'database'  => require_once APP_PATH.'/config/database.php',
        'modules'   => require_once APP_PATH.'/config/modules.php',
        'logsPath'  => '/data/logs/'.APP_NAME.'/',
        'viewsDir'  => BASE_PATH.'/public/views',

        // This allows the baseUri to be understand project paths that are not in the root directory
        // of the webpspace.  This will break if the public/index.php entry point is moved or
        // possibly if the web server rewrite rules are changed. This can also be set to a static path.
//         'baseUri'        => preg_replace('/public([\/\\\\])index.php$/', '', $_SERVER["PHP_SELF"]),
    ]
]);
