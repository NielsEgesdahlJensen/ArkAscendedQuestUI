<?php
namespace QuestApi\Helpers;

use QuestApi\Controllers\DatabaseController;
use QuestApi\Utils\Formatter;

class GetQuest {
    public string $eos_id;
    public int $questId;
    public array|NULL $quest;

    public function __construct(string $eos_id, int $questId) {
        $this->eos_id = $eos_id;
        $this->questId = $questId;
        $this->quest = $this->getQuest();
    }

    public function getQuest() : array|NULL {
        $db = DatabaseController::getConnection();
        $quest = $db->query("
                                    SELECT 
                                    	lethalquestsascended_quests_status.QuestID as QuestID,
                                    	lethalquestsascended_quests.Name as Name,
                                        lethalquestsascended_quests.Description as Description
                                    FROM
                                    	lethalquestsascended_quests_status
                                    LEFT JOIN
                                    	lethalquestsascended_quests
                                    ON
                                    	lethalquestsascended_quests.ID = lethalquestsascended_quests_status.QuestID    
                                    WHERE
                                    	lethalquestsascended_quests_status.eos_id = %s
                                    AND
                                    	lethalquestsascended_quests_status.QuestType = 0
                                    AND
                                        lethalquestsascended_quests_status.Completed = 0
                                    AND
                                        lethalquestsascended_quests_status.QuestID = %d                                   
                                    ",
                                    $this->eos_id,
                                    $this->questId);

        if (empty($quest)) {
            return NULL;
        }
        else {
            $returnArray = [];
            $playerStats = (new GetPlayerStats($this->eos_id))->stats;
            $questsId = array_column($quest, 'QuestID');

            $allQuestsRequirements = (new GetAllQuestsRequirements($questsId))->allQuestsRequirements;

            foreach ($quest as $key => $value) {
                $questId = $value['QuestID'];
                $progressArray = [];

                $requirements = $allQuestsRequirements[$questId];
                $requirementNames = array_keys($requirements);
                $requirementValues = array_values($requirements);

                foreach ($requirementNames as $index => $requirementName) {
                    $progress = $playerStats[$requirementName];
                    $requirement = preg_replace("/[^0-9]/", '', $requirementValues[$index]);

                    $progress = $progress > $requirement ? $requirement : $progress;

                    array_push($progressArray, [
                        'Name' => Formatter::statName($requirementName),
                        'Progress' => $progress.'/'.$requirement,
                        'Percentage' => $progress > 0 ? floor(($progress / $requirement) * 100) : 0
                    ]);
                }

                $returnArray['Name'] = $value['Name'];
                $returnArray['Progress'] = $progressArray;
            }
        }
        return $returnArray;
    }
}