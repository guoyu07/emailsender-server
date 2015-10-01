<?php
ini_set('yaf.cache_config', 0);
ini_set('yaf.name_suffix', 0);
ini_set('yaf.name_separator', '_');
ini_set('yaf.forward_limit', 0);
ini_set('yaf.use_spl_autoload', 1);
ini_set('yaf.lowcase_path', 1);

// ini_set("display_errors","On");error_reporting(E_ALL); 不到万不得已不要开这句
define('ROOT_PATH',               dirname(dirname(__FILE__)));
define('PUBLIC_PATH',             ROOT_PATH . '/public');
define('VENDOR_PATH',             ROOT_PATH . '/vendor');
define('APPLICATION_PATH',        ROOT_PATH . '/application');
define('APPLICATION_IS_CLI',      (php_sapi_name() == 'cli') ? true : false);
require_once VENDOR_PATH . "/autoload.php";
$application = new Yaf_Application( APPLICATION_PATH . "/config/application.ini", YAF_ENVIRON);
$application->bootstrap();
if ( ! defined('APPLICATION_NOT_RUN') ) {
    $application->run();
}