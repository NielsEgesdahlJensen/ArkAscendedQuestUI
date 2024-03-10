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
use React\Http\Browser;
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

        $router->get(
            '/quest/{questId}',
            New \QuestApi\Endpoints\Quest()
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

$client = new Browser();


echo "Checking connection to port $servicePort... \n";

$client->get('https://api.ipify.org?format=json', [
    'Content-Type' => 'application/json'
])->then(
    function ($response) use ($client, $servicePort) {
        $ip = json_decode($response->getBody())->ip;
        $data['ip'] = $ip;

        $data = [
            'host' => $ip,
            'ports' => [
                $servicePort
            ]
        ];
        $client->post('https://portchecker.io/api/v1/query', [
            'Content-Type' => 'application/json'
        ], json_encode($data))->then(
            function ($response) use ($ip) {
                $servicePort = json_decode($response->getBody())->check[0]->port;

                $firewallStatus = json_decode($response->getBody())->check[0]->status ? "open" : "CLOSED!!!! - Please check your firewall settings.";
                echo "Firewall status: " . $firewallStatus . PHP_EOL;
                echo "Server running at http://$ip:$servicePort/" . PHP_EOL;
            },
            function ($e) {
                echo $e->getMessage();
            }
        );
    },
    function ($e) {
        echo $e->getMessage();
    }
);

