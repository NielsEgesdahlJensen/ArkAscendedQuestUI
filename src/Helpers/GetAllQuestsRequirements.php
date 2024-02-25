<?php
namespace QuestApi\Helpers;

use QuestApi\Controllers\DatabaseController;

class GetAllQuestsRequirements {
    public $questIds;
    public $allQuestsRequirements = [];
    public function __construct(array $questIds) {
        $this->questIds = $questIds;
        $this->allQuestsRequirements = $this->getAllQuestsRequirements();
    }

    public function getAllQuestsRequirements() : array {
        $db = DatabaseController::getConnection();

        $ignoredColumns = ['rowid', 'ID', 'Name', 'Description'];

        $questsRequirements = $db->query("
                                    SELECT
                                        *
                                    FROM
                                        lethalquestsascended_quests
                                    WHERE
                                        ID IN %li                               
                                    ",
                                    $this->questIds
                                );

        $filteredQuestRequirements = [];
        foreach ($questsRequirements as $questRequirements) {
            $filteredQuestRequirements[$questRequirements['ID']] = array_filter(
                $questRequirements,
                function ($value, $key) use ($ignoredColumns) {
                    return $value > 0 && !in_array($key, $ignoredColumns);
                },
                ARRAY_FILTER_USE_BOTH
            );
        }

        return $filteredQuestRequirements;
    }
}