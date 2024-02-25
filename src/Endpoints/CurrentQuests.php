<?php

namespace QuestApi\Endpoints;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tnapf\Router\Routing\RouteRunner;
use HttpSoft\Response\JsonResponse;
use QuestApi\Helpers\GetCurrentQuests;
use Tnapf\Router\Interfaces\ControllerInterface;
use QuestApi\ResponseObjects\CurrentQuestsResponse;
use QuestApi\ResponseObjects\ErrorReponse;

class CurrentQuests implements ControllerInterface
{
    public function handle(
        ServerRequestInterface $request,
        ResponseInterface $response,
        RouteRunner $route,
        ): ResponseInterface {
            $eos_id = $route->args->EOS_ID;

            $currentQuests = (new GetCurrentQuests($eos_id))->currentQuests;

            if (count($currentQuests) === 0) {
                return new JsonResponse([
                    new ErrorReponse($eos_id, 'CurrentQuests', "No quests found..")
                ], 404);
            }

            $responseObject = new CurrentQuestsResponse($eos_id);
            $responseObject->CurrentQuests = $currentQuests;
            return new JsonResponse($responseObject, 200);
        }
}