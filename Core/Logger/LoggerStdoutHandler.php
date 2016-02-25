<?php
/**
 * Created by PhpStorm.
 * User: zqhong
 * Date: 2016/2/25
 * Time: 11:47
 */

namespace Core\Logger;


class LoggerStdoutHandler implements LoggerHandlerInterface
{
    public function handle($record)
    {
        echo $record;
    }
}