<?php
namespace QuestApi\Helpers;

use QuestApi\Controllers\DatabaseController;

class GetSpecialStats {
    public string $eos_id;
    public string $questType;
    public array|NULL $specialStats = NULL;

    public function __construct(string $eos_id, string $questType) {
        if ($questType === 'daily' || $questType === 'weekly') {
            $this->eos_id = $eos_id;
            $this->questType = $questType;
            $this->specialStats = $this->getSpecialStats();
        }
        else {
            throw new \Exception("Invalid quest type");
        }
    }

    public function getSpecialStats() : array|NULL {
        $db = DatabaseController::getConnection();
        $specialStats = $db->queryFirstRow("
                                    SELECT
                                        *
                                    FROM
                                        lethalquestsascended_stats_".$this->questType."
                                    WHERE
                                        eos_id = %s
                                    ",
                                    $this->eos_id
                                    );
        return (empty($specialStats)) ? NULL : $specialStats;
    }
}