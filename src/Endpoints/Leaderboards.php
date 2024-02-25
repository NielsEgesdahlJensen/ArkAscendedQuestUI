<?php

namespace QuestApi\Endpoints;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tnapf\Router\Routing\RouteRunner;
use HttpSoft\Response\JsonResponse;
use QuestApi\Helpers\GetLeaderboards;
use Tnapf\Router\Interfaces\ControllerInterface;
use QuestApi\ResponseObjects\LeaderboardsResponse;

class Leaderboards implements ControllerInterface
{
    public function handle(
        ServerRequestInterface $request,
        ResponseInterface $response,
        RouteRunner $route,
        ): ResponseInterface {
            $eos_id = $route->args->EOS_ID;

            $leaderboards = new GetLeaderboards($eos_id);
            $playerLeaderboard = $leaderboards->PlayerLeaderboard;
            $tribeLeaderboard = $leaderboards->TribeLeaderboard;


            $responseObject = new LeaderboardsResponse($eos_id);
            $responseObject->PlayerLeaderboard = $playerLeaderboard;
            $responseObject->TribeLeaderboard = $tribeLeaderboard;
            return new JsonResponse($responseObject, 200);
        }
}