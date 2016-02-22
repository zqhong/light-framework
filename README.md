# Light-Framework
Light-Framework is a fast, simple PHP Framework.

## Installation
```bash
$ git clone https://github.com/zqhong/light-framework.git
```

## Usage
The database config file is located at *Config/database.php*. You can set database connection values by placiing database.php.
And then, set router rules. The router config file is located at *Config/routes.php*.


routes example
```
$router->get("/hello", function() {
    echo "hello world";
});


$router->get("/hello/:num/:num", function($num1, $num2) {
    echo "\$num1: {$num1}, \$num2: {$num2}", "<br />";
});

$router->post("/test", function() {
    echo "test page";
});
```

controller example
```php
namespace Application\Controllers;

use Core\Request;
use Core\View;

class HomeController extends BaseController
{
    public function actionIndex(Request $request)
    {
        // equal：$_GET["id"]
        $id = $request->get("id");

        // load 'home' view
        View::make("home")->withTitle("Information")
                           ->withContent("Hello World");
    }
}
```

## License
The Light Framework is licensed under the MIT license. See [License File](LICENSE.md) for more information.
