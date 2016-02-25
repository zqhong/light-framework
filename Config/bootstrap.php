<?php
/**
 * Created by PhpStorm.
 * User: zqhong
 * Date: 2016/2/22
 * Time: 16:10
 */

if (php_sapi_name() == "cli") {
    die("Current version isnot support PHP CLI Mode.");
}

// define path
define("CONFIG_DIR", __DIR__);
define("BASE_DIR", dirname(CONFIG_DIR));
define("PUBLIC_DIR", BASE_DIR . DIRECTORY_SEPARATOR . "Public");
define("APP_DIR", BASE_DIR . DIRECTORY_SEPARATOR . "Application");
define("CORE_DIR", BASE_DIR . DIRECTORY_SEPARATOR . "Core");
define("SERVICES_DIR", BASE_DIR . DIRECTORY_SEPARATOR . "Services");
define("TESTS_DIR", BASE_DIR . DIRECTORY_SEPARATOR . "Tests");
define("VENDOR_DIR", BASE_DIR . DIRECTORY_SEPARATOR . "vendor");
define("LOGS_DIR", BASE_DIR . DIRECTORY_SEPARATOR . "Logs");
define("VIEW_BASE_PATH", BASE_DIR . DIRECTORY_SEPARATOR . "Application/Views");

// 定义系统配置
define("INTERNAL_ENCODING", "UTF-8");
define("DEFAULT_TIMEZONE", "PRC");


// env: development or production
define("ENVIRONMENT", "development");

if (defined("ENVIRONMENT")) {
    switch(ENVIRONMENT) {
        case "development":
            error_reporting(E_ALL);
            @ini_set("display_errors", 1);
            break;

        case "production":
            error_reporting(0);
            @ini_set("display_errors", 0);
            @ini_set("expose_php", false);
            break;

        default:
            exit("The application enviroment is not set correctly.");
    }
}

// autoload
require_once VENDOR_DIR . DIRECTORY_SEPARATOR . "autoload.php";

// logger
$logger = Core\Logger\Logger::getInstance();
$logger->setLevel(\Core\Logger\Logger::DEBUG);
$logger->addHandler(new \Core\Logger\LoggerStreamHandler(LOGS_DIR . DIRECTORY_SEPARATOR . "application.log"));
$logger->info("light-framework start...");

// 设置内部字符编码为 UTF-8
mb_internal_encoding(INTERNAL_ENCODING);
$logger->info("setting default internal encoding: " . INTERNAL_ENCODING);

// 设置时区
@date_default_timezone_set(DEFAULT_TIMEZONE);
$logger->info("setting default timezone: " . DEFAULT_TIMEZONE);

// database config
require_once CONFIG_DIR . DIRECTORY_SEPARATOR . "database.php";

// use router
require_once CONFIG_DIR . DIRECTORY_SEPARATOR . "routes.php";



