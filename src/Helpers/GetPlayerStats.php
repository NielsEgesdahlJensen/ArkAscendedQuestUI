<?php
namespace QuestApi\Helpers;

use QuestApi\Controllers\DatabaseController;

class GetPlayerStats {
    public string $eos_id;
    public array|NULL $stats;

    public function __construct(string $eos_id) {
        $this->eos_id = $eos_id;
        $this->stats = $this->getStats();
    }

    public function getStats() : array|NULL {
        $db = DatabaseController::getConnection();
        $stats = $db->queryFirstRow("SELECT * FROM lethalquestsascended_stats WHERE eos_id = %s", $this->eos_id);
        return $stats;
    }
}