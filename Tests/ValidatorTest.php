<?php

/**
 * Created by PhpStorm.
 * User: zqhong
 * Date: 2016/3/3
 * Time: 17:57
 */
class ValidatorTest extends PHPUnit_Framework_TestCase
{
    public function testRequired()
    {
        $data = array(
            "username" => "akira",
        );
        $rules = array(
            "username"=> "required"
        );
        $validator = new \Core\Validator($data, $rules);
        $this->assertSame(true, $validator->success);
    }

    public function testNumeric()
    {
        $data = array(
            "age" => 13.5,
        );
        $rules = array(
            "age"=> "numeric"
        );
        $validator = new \Core\Validator($data, $rules);
        $this->assertSame(true, $validator->success);
    }

    public function testInteger()
    {
        $data = array(
            "age" => 20,
        );
        $rules = array(
            "age" => "integer",
        );
        $validator = new \Core\Validator($data, $rules);
        $this->assertSame(true, $validator->success);
    }

    public function testMin()
    {
        $data = array(
            "username" => "akira1",             // length: 6
        );
        $rules = array(
            "age" => "min:6",
        );
        $validator = new \Core\Validator($data, $rules);
        $this->assertSame(true, $validator->success);
    }

    public function testMax()
    {
        $data = array(
            "username" => "akira111",       // length: 8
        );
        $rules = array(
            "username" => "max:8",
        );
        $validator = new \Core\Validator($data, $rules);
        $this->assertSame(true, $validator->success);
    }

    public function testEmail()
    {
        $data = array(
            "email" => "a@163.com",
        );
        $rules = array(
            "email" => "email",
        );
        $validator = new \Core\Validator($data, $rules);
        $this->assertSame(true, $validator->success);
    }
}
