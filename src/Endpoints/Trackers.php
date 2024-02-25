<?php
namespace QuestApi\Endpoints;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tnapf\Router\Routing\RouteRunner;
use HttpSoft\Response\JsonResponse;
use QuestApi\Helpers\GetAllTrackers;
use Tnapf\Router\Interfaces\ControllerInterface;
use QuestApi\ResponseObjects\ErrorReponse;
use QuestApi\ResponseObjects\TrackersResponse;

class Trackers implements ControllerInterface
{
    public function handle(
        ServerRequestInterface $request,
        ResponseInterface $response,
        RouteRunner $route,
        ): ResponseInterface {
            $eos_id = $route->args->EOS_ID;

            $allTrackers = (new GetAllTrackers($eos_id))->allTrackers;

            if (empty($allTrackers)) {
                return new JsonResponse([
                    new ErrorReponse($eos_id, 'Trackers', "Player not found.")
                ], 404);
            }

            $responseObject = new TrackersResponse($eos_id);
            $responseObject->Trackers = $allTrackers;
            return new JsonResponse($responseObject, 200);
        }
}