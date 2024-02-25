<?php
namespace QuestApi\Helpers;

use QuestApi\Controllers\ConfigController;
use QuestApi\Controllers\DatabaseController;
use QuestApi\Utils\Formatter;

class GetLeaderboards {
    public string $eos_id;
    public ?array $PlayerLeaderboard;
    public ?array $TribeLeaderboard;

    public function __construct(string $eos_id) {
        $this->eos_id = $eos_id;
        $this->PlayerLeaderboard = $this->getLeaderboard('player');
        $this->TribeLeaderboard = $this->getLeaderboard('tribe');
    }

    public function getLeaderboard(string $type) : array|NULL {
        $db = DatabaseController::getConnection();
        $config = (new ConfigController)->get();
        $myTribe = (new GetTribeFromEosID($this->eos_id))->TribeName;

        $leaderboardTrackers = $config['leaderboardTrackers'];

        $leaderboardTrackersSql = '';

        switch($type) {
            case 'player':
                $leaderboardTrackersSql = implode(',', $leaderboardTrackers);
                break;
            case 'tribe':
                $leaderboardTrackersSqlArray = [];
                foreach($leaderboardTrackers AS $key => $leaderboardTracker) {
                    $leaderboardTrackersSqlArray[$key] = "SUM($leaderboardTracker) AS $leaderboardTracker";
                }
                $leaderboardTrackersSql = implode(', ', $leaderboardTrackers);
                break;
        }

        $sqlStatement = sprintf("
                            SELECT
                                eos_id,
                                Name,
                                TribeName,
                                %s
                            FROM
                                `lethalquestsascended_stats`
                            %s
                            %s
                            ",
                            $leaderboardTrackersSql,
                            $type == 'tribe' ? "WHERE TribeName != ''" : '',
                            $type == 'tribe' ? "GROUP BY TribeName" : '');

        $leaderboard = $db->query($sqlStatement);

        $returnArray = [];
        $i = 1;
        foreach($leaderboardTrackers as $leaderboardTracker) {
            $trackerReturnArray = array();
            usort($leaderboard,function($a,$b) use ($leaderboardTracker) {
                return $b[$leaderboardTracker] <=> $a[$leaderboardTracker];
            });

            $j = 0;
            foreach($leaderboard as $entry) {

                $name = ($type == 'player') ? $entry['Name'] : NULL;
                $tribeName = $entry['TribeName'];
                $trackerValue = $entry[$leaderboardTracker];
                $trackerName = Formatter::statName($leaderboardTracker);
            
                $shownName = ($type == 'player') ? $name." (".$tribeName.")" : $tribeName;
            
                $keyName = ($type == 'player') ? 'Name' : 'TribeName';

                if (($type == 'player' && $entry['eos_id'] == $this->eos_id) || ($type == 'tribe' && $tribeName == $myTribe)) {
                    $trackerReturnArray[] = array(
                        $keyName => $shownName,
                        'Value' => $trackerValue,
                        'Position' => $j+1,
                        'You' => true
                    );
                }

                else {
                    $trackerReturnArray[] = array(
                        $keyName => $shownName,
                        'Value' => $trackerValue,
                        'Position' => $j+1
                    );
                }


                $j++;
                if($j === 10) {
                    break;
                }
            }
            array_push($returnArray, [$trackerName => $trackerReturnArray]);
            $i++;
        }

        return $returnArray;
    }
}