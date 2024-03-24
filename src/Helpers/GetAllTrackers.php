<?php

namespace QuestApi\Helpers;

use QuestApi\Controllers\ConfigController;
use QuestApi\Utils\Formatter;

class GetAllTrackers
{
    public string $eos_id;
    public array|NULL $allTrackers;

    public function __construct(string $eos_id)
    {
        $this->eos_id = $eos_id;
        $this->allTrackers = $this->getAllTrackers();
    }

    public function getAllTrackers(): array|NULL
    {
        $allTrackers = (new GetPlayerStats($this->eos_id))->stats;

        if (empty($allTrackers)) {
            return NULL;
        } else {
            $config = (new ConfigController)->get();
            $ignoredTrackers = $config['ignoredTrackers'];

            foreach ($ignoredTrackers as $ignoredTracker) {
                unset($allTrackers[$ignoredTracker]);
            }

            $filteredAllTrackers = [];
            foreach ($allTrackers as $key => $value) {
                $filteredAllTrackers[Formatter::statName($key)] = $value;
            }
            $allTrackers = $filteredAllTrackers;
        }
        return $allTrackers;
    }
}
