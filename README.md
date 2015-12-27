[![Build Status](https://travis-ci.org/pavlakis/seaudi.svg)](https://travis-ci.org/pavlakis/seaudi)

# Semi-Automatic Dependency Injection

Resolving class dependencies which have already been set in the DI Container.

It is using a container compatible with the [Interop Container interface](https://github.com/container-interop/container-interop)  and by using Reflection, it tries to match type hints to keys in the container.

When passing a class name, it expects its dependencies to exist as fully qualified class named keys in that container.

e.g. Slim 3 example

### For a basic action class
```
namespace PHPMinds\Action;

use Slim\Views\Twig;
use Psr\Log\LoggerInterface;
use Slim\Http\Request;
use Slim\Http\Response;


class NotFoundAction
{
    private $view;
    private $logger;


    public function __construct(Twig $view, LoggerInterface $logger)
    {
        $this->view = $view;
        $this->logger = $logger;

    }

    public function dispatch(Request $request, Response $response, $args)
    {

        $this->view->render($response, '404.twig');
        return $response;
    }
}
```

### When we have passed its dependencies

```
// Slim 3 example
$container = $app->getContainer();

// Twig
$container['Slim\Views\Twig'] = function ($c) {
    $settings = $c->get('settings');
    $view = new \Slim\Views\Twig($settings['view']['template_path'], $settings['view']['twig']);

    // Add extensions
    $view->addExtension(new Slim\Views\TwigExtension($c->get('router'), $c->get('request')->getUri()));
    $view->addExtension(new Twig_Extension_Debug());

    return $view;
};

// monolog
$container['Psr\Log\LoggerInterface'] = function ($c) {
    $settings = $c->get('settings');
    $logger = new \Monolog\Logger($settings['logger']['name']);
    $logger->pushProcessor(new \Monolog\Processor\UidProcessor());
    $logger->pushHandler(new \Monolog\Handler\StreamHandler($settings['logger']['path'], \Monolog\Logger::DEBUG));
    return $logger;
};

```

### We can now resolve its dependencies directly

```
$injector = new \pavlakis\seaudi\Injector($container);

$injector->add('PHPMinds\Action\NotFoundAction');
```
