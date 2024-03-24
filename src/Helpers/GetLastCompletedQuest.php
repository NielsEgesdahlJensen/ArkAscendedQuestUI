<?php

namespace QuestApi\Helpers;

use QuestApi\Controllers\DatabaseController;
use QuestApi\Utils\Formatter;

class GetLastCompletedQuest
{
    public string $eos_id;
    public array|NULL $lastCompleted;

    public function __construct(string $eos_id)
    {
        $this->eos_id = $eos_id;
        $this->lastCompleted = $this->getLastCompleted();
    }

    public function getLastCompleted(): array|NULL
    {
        $db = DatabaseController::getConnection();
        $lastCompleted = $db->queryFirstRow(
            "
                                            SELECT 
    	                                        lethalquestsascended_quests_status.QuestID as 'Quest ID',
    	                                        COALESCE( lethalquestsascended_quests.Name, lethalquestsascended_quests_daily.Name, lethalquestsascended_quests_weekly.Name ) as Name,
                                                lethalquestsascended_quests_status.CompletedTimeStamp as TimeStamp
                                            FROM
                                            	lethalquestsascended_quests_status
                                            LEFT JOIN
                                            	lethalquestsascended_quests
                                            ON
                                            	lethalquestsascended_quests.ID = lethalquestsascended_quests_status.QuestID

                                            LEFT JOIN
                                            	lethalquestsascended_quests_daily
                                            ON
                                            	lethalquestsascended_quests_daily.ID = lethalquestsascended_quests_status.QuestID

                                            LEFT JOIN
                                            	lethalquestsascended_quests_weekly
                                            ON
                                            	lethalquestsascended_quests_weekly.ID = lethalquestsascended_quests_status.QuestID

                                            WHERE
                                                lethalquestsascended_quests_status.eos_id = %s
                                            AND
                                                lethalquestsascended_quests_status.Completed = 1
                                            ORDER BY
                                                lethalquestsascended_quests_status.CompletedTimeStamp
                                            DESC LIMIT 0,1
                                            ",
            $this->eos_id
        );
        $lastCompleted['TimeStamp'] = Formatter::unixTimeToHuman($lastCompleted['TimeStamp']);
        return $lastCompleted;
    }
}
