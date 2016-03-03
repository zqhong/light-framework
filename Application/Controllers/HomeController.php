<?php
/**
 * Created by PhpStorm.
 * User: zqhong
 * Date: 2016/2/16
 * Time: 18:02
 */

namespace Application\Controllers;

use Core\Request;
use Core\View;

class HomeController extends BaseController
{
    public function actionIndex(Request $request)
    {
//        View::make("home")->withTitle("Information")
//                           ->withContent("Hello World");
        echo "homecontroller";
    }
}
