<?php
require __DIR__ . '/vendor/autoload.php';

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\HttpServer;
use React\Http\Browser;
use React\Socket\SocketServer;
use Tnapf\Router\Router;
use Tnapf\Router\Routing\RouteRunner;
use Tnapf\Router\Exceptions\HttpNotFound;
use QuestApi\Endpoints\DiscordLink;
use QuestApi\Endpoints\PlayerStatistics;
use QuestApi\Utils\QuestApiInit;
use QuestApi\Controllers\ConfigController;
use QuestApi\Endpoints\CompletedQuests;
use QuestApi\Endpoints\CurrentQuests;
use QuestApi\Endpoints\Leaderboards;
use QuestApi\Endpoints\Trackers;


$init = new QuestApiInit();

$router = new Router();

$router->get(
    '/',
    static function (ServerRequestInterface $request, ResponseInterface $response): ResponseInterface {
        $response->getBody()->write('Quest API is running!');
        return $response;
    }
);

$router->group(
    '/{EOS_ID}',
    static function (Router $router): void {

        $router->post(
            '/discordlink',
            new DiscordLink()
        );

        $router->get(
            '/statistics',
            new PlayerStatistics()
        );

        $router->get(
            '/completed',
            new CompletedQuests()
        );

        $router->get(
            '/trackers',
            new Trackers()
        );

        $router->get(
            '/leaderboards',
            new Leaderboards()
        );

        $router->get(
            '/currentquests',
            new CurrentQuests()
        );

        $router->get(
            '/quest/{questId}',
            new \QuestApi\Endpoints\Quest()
        );
    }
);

$router->group(
    '/content',
    static function (Router $router): void {
        $router->get(
            '/{file}',
            static function (
                ServerRequestInterface $request,
                ResponseInterface $response,
                RouteRunner $route
            ) {
                $file = $route->getParameter("file");
                $response->getBody()->write(file_get_contents(__DIR__ . "/content/$file"));
                return $response;
            }
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
