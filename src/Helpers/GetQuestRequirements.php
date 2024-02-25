<?php
namespace QuestApi\Helpers;

use QuestApi\Controllers\DatabaseController;

class GetQuestRequirements {
    public int $questId;
    public string $questType;
    public array|NULL $questRequirements = NULL;

    public function __construct(int $questId, string $questType) {
        if ($questType === 'daily' || $questType === 'weekly' || $questType === 'normal') {
            $this->questId = $questId;
            $this->questType = $questType;
            $this->questRequirements = $this->getQuestRequirements();
        }
        else {
            throw new \Exception("Invalid quest type");
        }
    }

    public function getQuestRequirements() : array|NULL {
        $db = DatabaseController::getConnection();

        $ignoredColumns = ['rowid','ID', 'Name', 'Description'];

        if ($this->questType === 'normal') {
            $table = 'lethalquestsascended_quests';
        } else {
            $table = 'lethalquestsascended_quests_' . $this->questType;
        }

        $questRequirements = $db->queryFirstRow("
                                    SELECT
                                        *
                                    FROM
                                        ".$table."
                                    WHERE
                                        ID = %i                                
                                    ",
                                    $this->questId
                                );

        $filteredQuestRequirements = array_filter(
            $questRequirements,
            function ($value, $key) use ($ignoredColumns) {
                return $value > 0 && !in_array($key, $ignoredColumns);
            },
            ARRAY_FILTER_USE_BOTH
        );
        
        return $filteredQuestRequirements;
    }
}