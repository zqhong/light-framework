<?php
/**
 * Created by PhpStorm.
 * User: zqhong
 * Date: 2016/2/25
 * Time: 11:38
 */

namespace Core\Logger;

use Core\Logger\LoggerHandlerInterface;

class LoggerStreamHandler implements LoggerHandlerInterface
{
    /**
     * @var str
     */
    private $filepath;

    /**
     * @param $filepath str
     */
    public function __construct($filepath)
    {
        $this->filepath = $filepath;
    }

    /**
     * @param $record str
     */
    public function handle($record)
    {
        file_put_contents($this->filepath, $record, FILE_APPEND);
    }
}