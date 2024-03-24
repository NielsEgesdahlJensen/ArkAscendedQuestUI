<?php

namespace QuestApi\Endpoints;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tnapf\Router\Routing\RouteRunner;
use HttpSoft\Response\JsonResponse;
use QuestApi\Helpers\GetDiscordLinked;
use Tnapf\Router\Interfaces\ControllerInterface;
use QuestApi\Helpers\GetPlayerStats;
use QuestApi\Controllers\ConfigController;
use QuestApi\Helpers\GetLastCompletedQuest;
use QuestApi\Helpers\GetQuestRequirements;
use QuestApi\Helpers\GetSpecialQuest;
use QuestApi\Helpers\GetSpecialStats;
use QuestApi\Helpers\TimeFormatter;
use QuestApi\ResponseObjects\ErrorReponse;
use QuestApi\ResponseObjects\PlayerStatsResponse;
use QuestApi\Utils\Formatter;

class PlayerStatistics implements ControllerInterface
{
    public $infoType = 'PlayerStatistics';

    public function handle(
        ServerRequestInterface $request,
        ResponseInterface $response,
        RouteRunner $route,
    ): ResponseInterface {

        $eos_id = $route->args->EOS_ID;

        $playerStats = (new GetPlayerStats($eos_id))->stats;

        if ($playerStats === null) {
            $response = new JsonResponse(new ErrorReponse($eos_id, $this->infoType, "Player not found"), 404);
            return $response;
        }

        $config = (new ConfigController)->get();

        $getDiscordLinked = new GetDiscordLinked($eos_id);
        $discordLinked = $getDiscordLinked->discordLinked;

        $playerStats = array_intersect_key($playerStats, array_flip($config['playerStatistics']));

        $playerStatsFormatted = [];

        foreach ($playerStats as $key => $value) {
            $playerStatsFormatted[Formatter::statName($key)] =  $value;
        }

        $playerStats = $playerStatsFormatted;

        $dailyQuest = (new GetSpecialQuest($eos_id, 'daily'))->specialQuest;
        $dailyStats = (new GetSpecialStats($eos_id, 'daily'))->specialStats;
        if ($dailyQuest !== null && $dailyStats !== null) {
            $dailyQuestId = $dailyQuest['QuestID'];
            $timeLeft = ($dailyQuest['TimeStamp'] + 86400) - time();

            if ($dailyQuest['Completed'] === 1) {
                $progress = "Completed!";
            } else {
                $dailyQuestRequirements = (new GetQuestRequirements($dailyQuestId, 'daily'))->questRequirements;

                $progress = Formatter::createProgressString($dailyStats, $dailyQuestRequirements);
            }

            $dailyQuestObject = new \stdClass();
            $dailyQuestObject->Name = $dailyQuest['Name'];
            $dailyQuestObject->Progress = $progress;
            $dailyQuestObject->{'Time Left'} = TimeFormatter::secsToStr($timeLeft);
            $dailyQuestObject->Description = Formatter::sanitizeDescription($dailyQuest['Description']);
        }


        $weeklyQuest = (new GetSpecialQuest($eos_id, 'weekly'))->specialQuest;
        $weeklyStats = (new GetSpecialStats($eos_id, 'weekly'))->specialStats;

        if ($weeklyQuest !== null && $weeklyStats !== null) {
            $weeklyQuestId = $weeklyQuest['QuestID'];
            $timeLeft = ($weeklyQuest['TimeStamp'] + 604800) - time();

            if ($weeklyQuest['Completed'] === 1) {
                $progress = "Completed!";
            } else {
                $weeklyQuestRequirements = (new GetQuestRequirements($weeklyQuestId, 'weekly'))->questRequirements;

                $progress = Formatter::createProgressString($weeklyStats, $weeklyQuestRequirements);
            }

            $weeklyQuestObject = new \stdClass();
            $weeklyQuestObject->Name = $weeklyQuest['Name'];
            $weeklyQuestObject->Progress = $progress;
            $weeklyQuestObject->{'Time Left'} = TimeFormatter::secsToStr($timeLeft);
            $weeklyQuestObject->Description = Formatter::sanitizeDescription($weeklyQuest['Description']);
        }

        $lastCompletedQuest = (new GetLastCompletedQuest($eos_id))->lastCompleted;

        $responseObject = new PlayerStatsResponse($eos_id);
        $responseObject->DiscordLinked = $discordLinked;
        $responseObject->PlayerStatistics = $playerStats;
        $responseObject->DailyQuest = (isset($dailyQuestObject)) ? $dailyQuestObject : null;
        $responseObject->WeeklyQuest = (isset($weeklyQuestObject)) ? $weeklyQuestObject : null;
        $responseObject->LastCompletedQuest = (isset($lastCompletedQuest)) ? $lastCompletedQuest : null;

        $response = new JsonResponse($responseObject, 200);
        return $response;
    }
}
