<?php
/**
 * Created by PhpStorm.
 * User: zqhong
 * Date: 2016/2/25
 * Time: 11:36
 */

namespace Core\Logger;


interface LoggerHandlerInterface
{
    public function handle($record);
}