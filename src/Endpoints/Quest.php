<?php

namespace QuestApi\Endpoints;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tnapf\Router\Routing\RouteRunner;
use HttpSoft\Response\JsonResponse;
use QuestApi\Helpers\GetQuest;
use Tnapf\Router\Interfaces\ControllerInterface;
use QuestApi\ResponseObjects\ErrorReponse;
use QuestApi\ResponseObjects\QuestResponse;

class Quest implements ControllerInterface
{
    public function handle(
        ServerRequestInterface $request,
        ResponseInterface $response,
        RouteRunner $route,
        ): ResponseInterface {
            $eos_id = $route->args->EOS_ID;
            $questId = $route->args->questId;

            $quest = (new GetQuest($eos_id, $questId))->quest;

            if ( $quest === null ) {
                return new JsonResponse(
                    new ErrorReponse($eos_id, 'Quest', "Quest not found for user..")
                , 404);
            }

            $responseObject = new QuestResponse($eos_id);
            $responseObject->Quest = $quest;
            return new JsonResponse($responseObject, 200);
        }
}