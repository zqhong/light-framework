<?php
/**
 * Created by PhpStorm.
 * User: zqhong
 * Date: 2016/2/17
 * Time: 11:35
 */

namespace Application\Controllers;


class BaseController
{
    public function __construct()
    {

    }

    public function validate($data, $rules)
    {
        return new \Core\Validator($data, $rules);
    }

    public function __destruct()
    {
    }
}