<?php
/**
 * Created by PhpStorm.
 * User: zqhong
 * Date: 2016/2/4
 * Time: 16:32
 */

namespace Core;

class Router
{
    /**
     * @var array
     */
    private $routes = array();

    /**
     * @var array
     */
    private $methods = array();

    /**
     * @var array
     */
    private $callbacks = array();

    /**
     * @var
     */
    private $error_callback;

    /**
     * @var bool
     */
    private $found_route = false;

    /**
     * @var string
     */
    private $default_controller = "home";

    /**
     * @var string
     */
    private $default_action = "index";

    /**
     * @var array
     */
    private $patterns = array(
        ":num" => "\d+",
        ":any" => "\w+",
        ":all" => ".+",
    );

    /**
     * @param null $after after router dispatch, what function you want to do
     */
    public function dispatch($after = null)
    {
        $request = \Core\Request::getInstance();
        $uri = $request->getPathInfo();
        $curr_method = strtolower($request->getCurrMethod());

        foreach ($this->routes as $key => $route) {
            if ($this->found_route) {
                break;
            }
            $method = $this->methods[$key];

            // 精确匹配
            if ($uri == $route && $curr_method == $method) {
                $this->found_route = true;
                $callback = $this->callbacks[$key];
                call_user_func($callback);
            }

            // 模糊匹配
            if (substr($route, 0, 1) == "#") {
               $i =  preg_match_all($route, $uri, $matches);

                if (0 !== $i && $curr_method == $method) {
                    $this->found_route = true;
                    $callback = $this->callbacks[$key];
                    $matches = array_slice($matches, 1);
                    $r = array();
                    foreach ($matches as $m) {
                        $r[] = $m[0];
                    }
                    call_user_func_array($callback, $r);
                }
            }
        }

        // MVC 模式
        if (!$this->found_route) {
            $controller = $this->default_controller;
            $action = $this->default_action;

            $args = explode("/", $uri);
            $args = array_filter($args);
            $args = array_values($args);

            if (!empty($args)) {
                $c = $args[0];
                if ($this->isValidName($c)) {
                    $controller = $c;
                }

                if (isset($args[1]) && $this->isValidName($args[1])) {
                    $action = $args[1];
                }
            }

            // 命名规范：
            // 控制器 XxxxxController，首字母大写，以“Controller”结尾
            // 行为   actionXXXX，以“action”开头，后面的行为名称首字母大写
            // 名字要求：HomeController、actionIndex
            $controller = ucfirst($controller) . "Controller";
            $action = "action" . ucfirst($action);

            $controller = "\\Application\\Controllers\\" . $controller;

            try {
                if (!class_exists($controller)) {
                    throw new ClassNotFoundException("Class {$controller} not found");
                }
                $c = new $controller();
                $c->$action($request);
            } catch (ClassNotFoundException $e) {
                echo "Class not found: ".$e->message();
            }
        }

        if (!empty($after)) {
            call_user_func($after);
        }
    }

    /**
     * 检查用户提供的名字是否符合要求，即以 a-z、A-Z、“_”开头，并且只可以出现 数字、字母、下划线。
     * @param $name 要检查的名字
     * @return bool
     */
    public function isValidName($name)
    {
        $i = preg_match('/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/', $name);
        return boolval($i);
    }

    /**
     * 调用未定义的非静态方法，系统将会调用 __call 魔术方法处理。
     * @param $method   string 方法名
     * @param $params   array  参数
     */
    public function __call($method, $params)
    {
        $method = (string)$method;
        $params = (array)$params;

        $flag = false;
        if (count($params) < 2) {
            $flag = true;
        }

        $allow_method = array(
            "get",
            "post",
            "any",                  // get or post
        );
        if (!in_array($method, $allow_method)) {
            $flag = true;
        }

        if ($flag) {
            die("In \\Core\\Router Class, {$method} method need more than 2 parameters. ");
        }

        $uri = $params[0];
        $callback = $params[1];

        # 对于需要模糊匹配的路由，在 $uri 的前后添加“#” 加以区分
        $i = 0;
        foreach ($this->patterns as $key => $pattern) {
            $uri = str_replace($key, "({$pattern})", $uri, $count);
            $i += $count;
        }
        if ($i > 0) {
            $uri = ("#^" . $uri . "$#");
        }

        if ($method == "any") {
            $this->pushToArray($uri, "get", $callback);
            $this->pushToArray($uri, "post", $callback);
        } else {
            $this->pushToArray($uri, $method, $callback);
        }
    }

    /**
     * @param $uri  string
     * @param $method   string
     * @param $callback callback
     */
    public function pushToArray($uri, $method, $callback)
    {
        array_push($this->routes, $uri);
        array_push($this->methods, strtolower($method));
        array_push($this->callbacks, $callback);
    }

    /**
     * @param $callback callback
     */
    public function error($callback)
    {
        $this->error_callback = $callback;
    }
}
