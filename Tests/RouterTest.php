<?php

/**
 * Created by PhpStorm.
 * User: zqhong
 * Date: 2016/3/3
 * Time: 16:20
 */
class RouterTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        unset($_SERVER);
        \Core\Request::getInstance()->reset();
        $_SERVER["SERVER_PROTOCOL"] = "HTTP/1.1";
        $_SERVER["SCRIPT_NAME"] = "/index.php";
        $_SERVER["SCRIPT_FILENAME"] = "/path/to/php/bin/index.php";
        parent::setUp();
    }

    public function testGetDispatch()
    {
        $_SERVER["REQUEST_METHOD"] = "GET";

        _setEnv("/hello");
        $router = new \Core\Router();

        $this->expectOutputString("hello world");
        $router->get("/hello", function() {
            echo "hello world";
        });
        $router->dispatch();
    }

    public function testGetRegDispatch()
    {
        $_SERVER["REQUEST_METHOD"] = "GET";

        $excepted = "111";
        _setEnv("/article/{$excepted}");
        $router = new \Core\Router();

        $this->expectOutputString($excepted);
        $router->get("/article/:num", function($num) {
            echo $num;
        });
        $router->dispatch();
    }

    public function testPostDispatch()
    {
        $_SERVER["REQUEST_METHOD"] = "POST";

        _setEnv("/hello");
        $router = new \Core\Router();

        $this->expectOutputString("hello world");
        $router->post("/hello", function() {
            echo "hello world";
        });
        $router->dispatch();
    }

    public function testPostRegDispatch()
    {
        $_SERVER["REQUEST_METHOD"] = "POST";

        $excepted = "111";
        _setEnv("/article/{$excepted}");
        $router = new \Core\Router();

        $this->expectOutputString($excepted);
        $router->post("/article/:num", function($num) {
            echo $num;
        });
        $router->dispatch();
    }

    public function testAnyGetDispatch()
    {
        // get test
        $_SERVER["REQUEST_METHOD"] = "GET";

        _setEnv("/hello");
        $router = new \Core\Router();

        $this->expectOutputString("hello world");
        $router->any("/hello", function() {
            echo "hello world";
        });
        $router->dispatch();
    }

    public function testAnyPostDispatch()
    {
        // get test
        $_SERVER["REQUEST_METHOD"] = "POST";

        _setEnv("/hello");
        $router = new \Core\Router();

        $this->expectOutputString("hello world");
        $router->any("/hello", function() {
            echo "hello world";
        });
        $router->dispatch();

    }

    public function testMvc()
    {
        $_SERVER["REQUEST_METHOD"] = "GET";

        _setEnv("/home/index");
        $router = new \Core\Router();

        $this->expectOutputString("homecontroller");
        $router->dispatch();
    }
}
