<?php

namespace QuestApi\Helpers;

use QuestApi\Controllers\ConfigController;
use QuestApi\Controllers\DatabaseController;
use QuestApi\Utils\Formatter;

class GetLeaderboards
{
    public string $eos_id;
    public ?array $PlayerLeaderboard;
    public ?array $TribeLeaderboard;

    public function __construct(string $eos_id)
    {
        $this->eos_id = $eos_id;
        $this->PlayerLeaderboard = $this->getLeaderboard('player');
        $this->TribeLeaderboard = $this->getLeaderboard('tribe');
    }

    public function getLeaderboard(string $type): array|NULL
    {
        $db = DatabaseController::getConnection();
        $config = (new ConfigController)->get();
        $myTribe = (new GetTribeFromEosID($this->eos_id))->TribeName;

        $leaderboardTrackers = $config['leaderboardTrackers'];

        $sqlStatement = '';

        switch ($type) {
            case 'player':

                $leaderboard_Tracker_SQL = implode(',', $leaderboardTrackers);
                $sqlStatement = sprintf("
                SELECT
                    eos_id,
                    Name,
                    TribeName,
                %s
                FROM
                    `lethalquestsascended_stats`
                ", $leaderboard_Tracker_SQL);
                break;

            case 'tribe':
                $leaderboard_Tracker_SQL = '';
                foreach ($leaderboardTrackers as $key => $Tracker) {
                    $leaderboard_Tracker_SQL .= "SUM($Tracker) AS $Tracker";

                    end($leaderboardTrackers);
                    if ($key != key($leaderboardTrackers))
                        $leaderboard_Tracker_SQL .= ",";
                }
                $sqlStatement = sprintf("
                SELECT
                    TribeName,
                    %s
                FROM
                    `lethalquestsascended_stats`
                WHERE
                    TribeName != ''
                GROUP BY
                    TribeName
                ", $leaderboard_Tracker_SQL);
                break;
        }

        $leaderboard = $db->query($sqlStatement);

        $returnArray = [];
        $i = 1;
        foreach ($leaderboardTrackers as $leaderboardTracker) {
            $trackerReturnArray = array();
            usort($leaderboard, function ($a, $b) use ($leaderboardTracker) {
                return $b[$leaderboardTracker] <=> $a[$leaderboardTracker];
            });

            $j = 0;
            foreach ($leaderboard as $entry) {

                $name = ($type == 'player') ? $entry['Name'] : NULL;
                $tribeName = $entry['TribeName'];
                $trackerValue = $entry[$leaderboardTracker];
                $trackerName = Formatter::statName($leaderboardTracker);

                $shownName = ($type == 'player') ? $name . " (" . $tribeName . ")" : $tribeName;

                $keyName = ($type == 'player') ? 'Name' : 'TribeName';

                if (($type == 'player' && $entry['eos_id'] == $this->eos_id) || ($type == 'tribe' && $tribeName == $myTribe)) {
                    $trackerReturnArray[] = array(
                        $keyName => $shownName,
                        'Value' => $trackerValue,
                        'Position' => $j + 1,
                        'You' => true
                    );
                } else {
                    $trackerReturnArray[] = array(
                        $keyName => $shownName,
                        'Value' => $trackerValue,
                        'Position' => $j + 1
                    );
                }


                $j++;
                if ($j === 10) {
                    break;
                }
            }
            array_push($returnArray, [$trackerName => $trackerReturnArray]);
            $i++;
        }

        $page = 1;
        $newReturnArray = [];
        foreach ($returnArray as $key => $value) {
            $newReturnArray[$page][] = $value;
            if (count($newReturnArray[$page]) === 5) {
                $page++;
            }
        }
        return $newReturnArray;
    }
}
