<?php

namespace QuestApi\Helpers;

use QuestApi\Controllers\DatabaseController;

class GetSpecialQuest
{
    public string $eos_id;
    public string $questType;
    public int $questTypeId;
    public array|NULL $specialQuest = NULL;

    public function __construct(string $eos_id, string $questType)
    {
        if ($questType === 'daily' || $questType === 'weekly') {
            $this->eos_id = $eos_id;
            $this->questType = $questType;
            $this->questTypeId = $questType === 'daily' ? 1 : 2;
            $this->specialQuest = $this->getSpecialQuest();
        } else {
            throw new \Exception("Invalid quest type");
        }
    }

    public function getSpecialQuest(): array|NULL
    {
        $db = DatabaseController::getConnection();
        $specialQuest = $db->queryFirstRow(
            "
                                    SELECT 
                                        lethalquestsascended_quests_status.QuestID as QuestID,
                                        lethalquestsascended_quests_%l.Name as Name,
                                        lethalquestsascended_quests_%l.Description as Description,
                                        lethalquestsascended_quests_status.Completed as Completed,
                                        lethalquestsascended_quests_status.TimeStamp as TimeStamp
                                    FROM
                                        lethalquestsascended_quests_status
                                    INNER JOIN
                                        lethalquestsascended_quests_%l
                                    ON
                                        lethalquestsascended_quests_%l.ID = lethalquestsascended_quests_status.QuestID
                                    WHERE
                                        lethalquestsascended_quests_status.eos_id = %s
                                    AND
                                        lethalquestsascended_quests_status.QuestType = %i
                                    ORDER BY lethalquestsascended_quests_status.TimeStamp DESC LIMIT 1",
            $this->questType,
            $this->questType,
            $this->questType,
            $this->questType,
            $this->eos_id,
            $this->questTypeId
        );
        return $specialQuest;
    }
}
