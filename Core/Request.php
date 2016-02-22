<?php
/**
 * Created by PhpStorm.
 * User: zqhong
 * Date: 2016/2/22
 * Time: 16:21
 */

namespace Core;


/**
 * Class Request
 * @package Core
 */
class Request
{
    /**
     * @param $array
     * @param string $index
     * @return bool
     */
    protected function _FetchFromArray($array, $index = '')
    {
        if (!isset($array[$index])) {
            return false;
        }
        return $array[$index];
    }

    /**
     * @param array $data
     * @param null $index
     * @return array|bool
     */
    protected function _process($data = array(), $index = null)
    {
        if ($index === null && !empty($data)) {
            $get = array();

            foreach (array_keys($data) as $key) {
                $get[$key] = $this->_FetchFromArray($data, $key);
            }
            return $get;
        }

        return $this->_FetchFromArray($data, $index);
    }

    /**
     * @param null $index
     * @return array|bool
     */
    public function get($index = null)
    {
        return $this->_process($_GET, $index);
    }

    /**
     * @param null $index
     * @return array|bool
     */
    public function post($index = null)
    {
        return $this->_process($_POST, $index);
    }

    /**
     * @param null $index
     * @return array|bool
     */
    public function getPost($index = null)
    {
        return $this->_process($_REQUEST);
    }
}