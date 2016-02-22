<?php
/**
 * Created by PhpStorm.
 * User: zqhong
 * Date: 2016/2/17
 * Time: 12:01
 */

namespace Core;


class View
{
    /**
     * @var string 视图名称（绝对路径）
     */
    protected $view_name;

    /**
     * @var array 装载数据
     */
    protected $data;

    /**
     * @var bool 是否返回json数据
     */
    protected $is_json = false;

    /**
     * @param $view_name
     * @param array $data
     * @param bool|false $is_json
     */
    public function __construct($view_name, $data = array(), $is_json = false)
    {
        $this->view_name = $view_name;
        $this->data       = $data;
        $this->is_json   = $is_json;
    }

    /**
     * 返回一个 \Core\View 实例
     *
     * @param $view_name
     * @param array $data
     * @param bool|false $is_json
     * @return View
     */
    public static function make($view_name, $data = array(), $is_json = false)
    {
        $path = self::getFilePath($view_name);
        return new \Core\View($path, $data, $is_json);
    }

    /**
     * 将用户传递的数据保存到 $this->data 属性中
     *
     * @param $var_name
     * @param $var_value
     * @return $this
     */
    public function with($var_name, $var_value)
    {
        $this->data[$var_name] = $var_value;

        return $this;
    }

    /**
     * 利用 __call 魔术方法，方便用户使用 with方法。例如：$this->withVarname(value) 等同于 $this->with("varname", value)
     *
     * @param $method
     * @param $params
     */
    public function __call($method, $params)
    {
        if (substr($method, 0, 4) == "with") {
            $var_name = lcfirst(substr($method, 4));
            $this->with($var_name, (string)$params[0]);
            return $this;
        }
    }

    /**
     * 获取视图的绝对路径
     *
     * @param $view_name 视图名
     * @return string
     */
    public static function getFilePath($view_name)
    {
        if (!defined("VIEW_BASE_PATH")) {
            die("VIEW_BASE_PATH constant is undefined");
        }

        $path = VIEW_BASE_PATH . DIRECTORY_SEPARATOR . $view_name . ".php";
        if (!file_exists($path)) {
            die("{$path} file is not found");
        }

        return $path;
    }

    /**
     * \Core\View 类被回收的时候，将数据输出。
     * 当 $this->is_json 为 true，输出 json 数据u；否则，输出 html 数据。
     *
     */
    public function __destruct()
    {
        if ($this->is_json) {
            echo json_encode($this->data);
        } else {
            extract($this->data);
            require $this->view_name;
        }
    }
}