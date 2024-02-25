<?php 
namespace QuestApi\Helpers;

use QuestApi\Controllers\DatabaseController;

class GetTribeFromEosID {
    public string $eos_id;
    public string $TribeName;

    public function __construct(string $eos_id) {
        $this->eos_id = $eos_id;
        $this->TribeName = $this->getTribe();
    }

    public function getTribe() : string {
        $db = DatabaseController::getConnection();
        $tribe = $db->queryFirstField("
                    SELECT
                        TribeName
                    FROM
                        lethalquestsascended_stats
                    WHERE
                        eos_id = %s
                    ",
                    $this->eos_id);
        return $tribe;
    }
}