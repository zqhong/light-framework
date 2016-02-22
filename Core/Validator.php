<?php
/**
 * Created by PhpStorm.
 * User: zqhong
 * Date: 2016/2/17
 * Time: 15:19
 */

namespace Core;


class Validator
{
    /**
     * @var bool 验证结果
     */
    public $success = true;

    /** 验证数据
     *
     * @var array
     */
    protected $data;

    /**
     * 验证规则
     *
     * @var array
     */
    protected $rules;

    /**
     * 验证错误信息
     *
     * @var array
     */
    public $errors = array();

    /**
     * 验证错误提示
     *
     * @var array
     */
    protected $error_reasons = array(
        "email"     => "The :attribute format is invalid.",
        "min"       => "The :attribute must be at least :min.",
        "max"       => "The :attribute may not be greater than :max.",
        "required" => "The :attribute field is required.",
        "numeric"  => "The :attribute field must be number.",
        "integer"  => "The :attribute field must be integer.",
    );

    /**
     * @param $data
     * @param $rules
     */
    public function __construct($data, $rules)
    {
        $this->data  = (array)$data;
        $this->rules = (array)$rules;
        $this->validate();
    }

    /**
     * 验证方法
     *
     */
    public function validate()
    {
        foreach ($this->rules as $attr => $rule) {
            if (!isset($this->data[$attr])) {
                continue;
            }

            $value = $this->data[$attr];
            foreach (explode("|", $rule) as $item) {
                $details = explode(":", $item);

                $num = count($details);
                if (0 === $num) {
                    continue;
                }

                $method = (string)$details[0];
                if ($num > 1) {
                    $reason = $this->$method($value, $details[1]);
                } else {
                    $reason = $this->$method($value);
                }

                if ($reason !== true) {
                    $this->errors[] = str_replace(":attribute", $attr, $reason);
                }
            }
        }

        if (count($this->errors)) {
            $this->success = false;
        }
    }

    /**
     * Required
     *
     * @param $value
     * @return bool|string
     */
    public function required($value)
    {
        if (!is_array($value)) {
            return (trim($value) == "") ? $this->error_reasons["required"] : true;
        }

        return (empty($value)) ? $this->error_reasons["required"] : true;
    }

    /**
     * Numeric
     *
     * @param $value
     * @return bool|string
     */
    public function numeric($value)
    {
        return is_numeric($value) ? true : $this->error_reasons["numeric"];
    }

    /**
     * Integer
     *
     * @param $value
     * @return bool|string
     */
    public function integer($value)
    {
        return is_integer($value) ? true : $this->error_reasons["integer"];
    }

    /**
     * Min
     *
     * @param $value
     * @param $min
     * @return bool|string
     */
    public function min($value, $min)
    {
        $num = mb_strlen($value);
        return $num >= $min ? true : str_replace(":min",$num, $this->error_reasons["min"]);;
    }

    /**
     * Max
     *
     * @param $value
     * @param $max
     * @return bool|string
     */
    public function max($value, $max)
    {
        $num = mb_strlen($value);
        return $num >= $max ? true : str_replace(":min",$num, $this->error_reasons["max"]);;
    }

    /**
     * Email
     *
     * @param $value
     * @return bool|string
     */
    public function email($value)
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL) ? true : $this->error_reasons["email"];
    }

    /**
     * @param $method
     * @param $params
     */
    public function __call($method, $params)
    {
        die("Validator class has not {$method} method");
    }
}