<?php

/**
 * Created by PhpStorm.
 * User: zqhong
 * Date: 2016/3/2
 * Time: 15:33
 */

class RequestTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Core\Request 实例句柄
     */
    protected $_request = null;

    /**
     * 构造函数
     *
     * @param null $name
     * @param array $data
     * @param string $dataName
     */
    public function __construct($name = null, array $data = array(), $dataName = '')
    {
        $this->_request = \Core\Request::getInstance();

        // necessary
        parent::__construct($name, $data, $dataName);
    }

    /**
     * 负责初始化环境（fixture）
     */
    protected function setUp()
    {
        $_SERVER["REQUEST_METHOD"] = "GET";
        $_SERVER["SERVER_PROTOCOL"] = "HTTP/1.1";
        $_SERVER["SCRIPT_NAME"] = "/index.php";
        $_SERVER["SCRIPT_FILENAME"] = "/path/to/php/bin/index.php";

        parent::setUp();
    }

    /**
     * 负责清理测试环境
     */
    protected function tearDown()
    {
        unset($_SERVER);
        $this->_request->reset();

        parent::tearDown();
    }

    /**
     * @dataProvider pathinfoProvider
     */
    public function testGetPathInfo($uri, $excepted)
    {
        _setEnv($uri);
        $this->assertEquals($excepted, $this->_request->getPathInfo());
    }

    /**
     * 数据提供器
     *
     * @return array
     */
    public function pathinfoProvider()
    {
        return array(
            // 第一个值：$uri、第二个值：$excepted
            array("/index.php", "/"),
            array("/index.php/1/2/3", "/1/2/3"),
            array("/index.php/4/5/6?k=v", "/4/5/6"),
            array("/", "/"),
            array("/1/2/3", "/1/2/3"),
            array("/4/5/6?k=v", "/4/5/6"),

            // 测试畸形 uri
            array("//////", "/"),
            array("///index.php//1/2/333", "/1/2/333"),
        );
    }
}
