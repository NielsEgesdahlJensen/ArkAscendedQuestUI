<?php

namespace QuestApi\Helpers;

use QuestApi\Controllers\DatabaseController;
use QuestApi\Utils\Formatter;

class GetCompletedQuests
{
    public string $eos_id;
    public array|NULL $completedQuests;

    public function __construct(string $eos_id)
    {
        $this->eos_id = $eos_id;
        $this->completedQuests = $this->getCompletedQuests();
    }

    public function getCompletedQuests(): array|NULL
    {
        $db = DatabaseController::getConnection();
        $completedQuests = $db->query(
            "
                                    SELECT 
                                    	lethalquestsascended_quests_status.QuestID as 'Quest ID',
                                    	lethalquestsascended_quests.Name as Name,
                                        lethalquestsascended_quests.Description as Description,
                                    	COALESCE(lethalquestsascended_quests_status.CompletedTimeStamp,0) as TimeStamp
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
                                        lethalquestsascended_quests_status.Completed = 1 ORDER BY lethalquestsascended_quests_status.QuestID ASC                                        
                                    ",
            $this->eos_id
        );

        if (count($completedQuests) === 0) {
            return NULL;
        } else {
            foreach ($completedQuests as $key => $value) {
                $completedQuests[$key]['Description'] = Formatter::sanitizeDescription($value['Description']);
                $completedQuests[$key]['TimeStamp'] = Formatter::unixTimeToHuman($value['TimeStamp']);
            }
        }
        return $completedQuests;
    }
}
