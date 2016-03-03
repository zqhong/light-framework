<?php
/**
 * Created by PhpStorm.
 * User: zqhong
 * Date: 2016/2/4
 * Time: 17:08
 */

$logger->info("setting routes...");
$router = new \Core\Router();

// get方法，完整匹配（示例）
$router->get("/hello", function() {
    echo "hello world";
});


// get方法，正则表达式匹配（示例）
$router->get("/hello/:num/:num", function($num1, $num2) {
    echo "\$num1: {$num1}, \$num2: {$num2}", "<br />";
});


// 除此之外，使用 controller/method 的方式匹配 uri
$logger->info("router start dispatch ...");
$router->dispatch();