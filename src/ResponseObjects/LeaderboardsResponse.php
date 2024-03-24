<?php

namespace QuestApi\ResponseObjects;

use QuestApi\ResponseObjects\ResponseObject;

class LeaderboardsResponse extends ResponseObject
{
    public $PlayerLeaderboard;
    public $TribeLeaderboard;
    public function __construct(string $eos_id)
    {
        $this->InfoType = "Leaderboards";
        parent::__construct($eos_id);
    }
}
