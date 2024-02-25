<?php
namespace QuestApi\ResponseObjects;
use QuestApi\ResponseObjects\ResponseObject;

class PlayerStatsResponse extends ResponseObject {
    public $DiscordLinked;
    public $PlayerStatistics;
    public $DailyQuest;
    public $WeeklyQuest;
    public $LastCompletedQuest;

    public function __construct(string $eos_id) {
        $this->InfoType = "PlayerStats";
        parent::__construct($eos_id);
    }
}