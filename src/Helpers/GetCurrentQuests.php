<?php

namespace QuestApi\Helpers;

use QuestApi\Controllers\DatabaseController;
use QuestApi\Utils\Formatter;

class GetCurrentQuests
{
    public string $eos_id;
    public array|NULL $currentQuests;

    public function __construct(string $eos_id)
    {
        $this->eos_id = $eos_id;
        $this->currentQuests = $this->getCurrentQuests();
    }

    public function getCurrentQuests(): array|NULL
    {
        $db = DatabaseController::getConnection();
        $currentQuests = $db->query(
            "
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
                                        lethalquestsascended_quests_status.Completed = 0 ORDER BY lethalquestsascended_quests_status.QuestID ASC                                       
                                    ",
            $this->eos_id
        );

        if (count($currentQuests) === 0) {
            return NULL;
        } else {
            $playerStats = (new GetPlayerStats($this->eos_id))->stats;
            $allQuestsIds = array_column($currentQuests, 'QuestID');

            $allQuestsRequirements = (new GetAllQuestsRequirements($allQuestsIds))->allQuestsRequirements;

            foreach ($currentQuests as $key => $value) {
                $questId = $value['QuestID'];
                $progressArray = [];
                $numRequired = 0;
                $numProgress = 0;

                $requirements = $allQuestsRequirements[$questId];
                $requirementNames = array_keys($requirements);
                $requirementValues = array_values($requirements);

                foreach ($requirementNames as $index => $requirementName) {
                    $progress = $playerStats[$requirementName];
                    $requirement = preg_replace("/[^0-9]/", '', $requirementValues[$index]);

                    $progress = $progress > $requirement ? $requirement : $progress;

                    $numRequired += $requirement;
                    $numProgress += $progress;

                    array_push($progressArray, [
                        'Name' => Formatter::statName($requirementName),
                        'Progress' => $progress . '/' . $requirement,
                        'Percentage' => $progress > 0 ? floor(($progress / $requirement) * 100) : 0
                    ]);
                }
                if ($numProgress === 0) {
                    $percentage = 0;
                } else {
                    $percentage = floor(($numProgress / $numRequired) * 100);
                }

                $currentQuests[$key]['Description'] = Formatter::sanitizeDescription($value['Description']);
                $currentQuests[$key]['OverallProgress'] = $percentage;
                $currentQuests[$key]['Progress'] = $progressArray;
            }
        }
        return $currentQuests;
    }
}
