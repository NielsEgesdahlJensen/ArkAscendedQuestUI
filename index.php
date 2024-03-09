<?php
require __DIR__ . '/vendor/autoload.php';

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\HttpServer;
use React\Socket\SocketServer;
use Tnapf\Router\Router;
use Tnapf\Router\Routing\RouteRunner;

use QuestApi\Endpoints\DiscordLink;
use QuestApi\Endpoints\PlayerStatistics;
use QuestApi\Utils\QuestApiInit;
use QuestApi\Controllers\ConfigController;
use QuestApi\Endpoints\CompletedQuests;
use QuestApi\Endpoints\CurrentQuests;
use QuestApi\Endpoints\Leaderboards;
use QuestApi\Endpoints\Trackers;
use Tnapf\Router\Exceptions\HttpNotFound;

$init = new QuestApiInit();

$router = new Router();

$router->group(
    '/{EOS_ID}',
    static function (Router $router): void {

        $router->post(
            '/discordlink',
            New DiscordLink()
        );

        $router->get(
            '/statistics',
            New PlayerStatistics()
        );

        $router->get(
            '/completed',
            New CompletedQuests()
        );

        $router->get(
            '/trackers',
            New Trackers()
        );

        $router->get(
            '/leaderboards',
            New Leaderboards()
        );

        $router->get(
            '/currentquests',
            New CurrentQuests()
        );
    }
);

$router->catch(
    HttpNotFound::class,
    static function (
      ServerRequestInterface $request,
      ResponseInterface $response,
      RouteRunner $route
    ) {
      $response->getBody()->write("{$request->getUri()->getPath()} does not exist");
      return $response;
    }
  );

$router->catch(
    Throwable::class,
    static function (
        ServerRequestInterface $request,
        ResponseInterface $response,
        RouteRunner $route
    ) {
        $exception = $route->exception;
        $exceptionMesssage = $exception->getMessage();
        $exceptionFile = $exception->getFile();
        $exceptionLine = $exception->getLine();
        $exceptionStackTrace = $exception->getTraceAsString();
        $exceptionString = "Exception: $exceptionMesssage\nFile: $exceptionFile\nLine: $exceptionLine\nStack Trace: $exceptionStackTrace\n";
        $response->getBody()->write($exceptionString);
        return $response->withHeader('Content-Type', 'text/plain');
    }
);

$http = new HttpServer(function (ServerRequestInterface $request) use ($router) {
    return $router->run($request);
});

$config = (new ConfigController)->get();
$servicePort = $config['servicePort']; 

$socket = new SocketServer("0.0.0.0:$servicePort");
$http->listen($socket);

echo "Server running at http://0.0.0.0:$servicePort" . PHP_EOL;