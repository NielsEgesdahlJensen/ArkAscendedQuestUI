<?php

namespace QuestApi\Endpoints;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tnapf\Router\Routing\RouteRunner;
use HttpSoft\Response\JsonResponse;
use Tnapf\Router\Interfaces\ControllerInterface;
use QuestApi\Helpers\GetCompletedQuests;
use QuestApi\ResponseObjects\CompletedQuestsResponse;
use QuestApi\ResponseObjects\ErrorReponse;

class CompletedQuests implements ControllerInterface
{
    public function handle(
        ServerRequestInterface $request,
        ResponseInterface $response,
        RouteRunner $route,
        ): ResponseInterface {
            $eos_id = $route->args->EOS_ID;

            $completedQuests = (new GetCompletedQuests($eos_id))->completedQuests;

            if (count($completedQuests) === 0) {
                return new JsonResponse([
                    new ErrorReponse($eos_id, 'CompletedQuests', "No quests completed.")
                ], 404);
            }

            $responseObject = new CompletedQuestsResponse($eos_id);
            $responseObject->CompletedQuests = $completedQuests;
            return new JsonResponse($responseObject, 200);
        }
}