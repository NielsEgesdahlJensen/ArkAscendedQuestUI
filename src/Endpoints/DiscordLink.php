<?php
namespace QuestApi\Endpoints;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tnapf\Router\Routing\RouteRunner;
use HttpSoft\Response\JsonResponse;
use Tnapf\Router\Interfaces\ControllerInterface;
use Exception;
use QuestApi\Controllers\DatabaseController;
use QuestApi\ResponseObjects\DiscordLinkResponse;

class DiscordLink implements ControllerInterface
{
    public function handle(
        ServerRequestInterface $request,
        ResponseInterface $response,
        RouteRunner $route,
        ): ResponseInterface {
           
            $postParams = json_decode($request->getBody()->getContents());

            if ( !isset($postParams->activationcode) ) {
                return new JsonResponse([
                    'error' => 'Activationcode not provided.'
                ], 400);
            }

            $eos_id = $route->args->EOS_ID;
            $code = $postParams->activationcode;

            $db = DatabaseController::getConnection();

            $existingUsers = $db->query("SELECT * FROM discordlink WHERE eos_id = %s OR activationcode = %s", $eos_id, $code);

            if ( !empty($existingUsers) ) {

                foreach ($existingUsers as $user) {
                    if ($user['eos_id'] == $eos_id && $user['discord_id'] != null) {
                        $responseObject = new DiscordLinkResponse($eos_id, 'EOS_ID already linked.');
                        unset($responseObject->ActivationCode);
                        return new JsonResponse($responseObject, 200);
                    }

                    else if ( $user ['eos_id'] == $eos_id && $user['activationcode'] != NULL ) {
                        if ( time() - $user['timestamp'] > (5*60) ) {
                            $db->delete("discordlink", "eos_id = %s", $eos_id);
                        }
                        else {
                            $responseObject = new DiscordLinkResponse($eos_id, 'EOS_ID already waiting for activation.');
                            $responseObject->ActivationCode = $user['activationcode'];
                            return new JsonResponse($responseObject, 200);
                        }
                    }
                }
            }

            try {
                $db->insert("discordlink", [
                    "eos_id" => $eos_id,
                    "activationcode" => $code,
                    "timestamp" => time()
                ]);
                
                $responseObject = new DiscordLinkResponse($eos_id);
                $responseObject->ActivationCode = $code;
                $response = new JsonResponse($responseObject, 200);

                return $response;
            }
            catch (Exception $e) {
                return new JsonResponse([
                    'error' => 'Database error: ' . $e->getMessage()
                ], 400);
            }
        }
}