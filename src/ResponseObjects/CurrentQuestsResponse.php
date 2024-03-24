<?php

namespace QuestApi\ResponseObjects;

use QuestApi\ResponseObjects\ResponseObject;

class CurrentQuestsResponse extends ResponseObject
{
    public $CurrentQuests;
    public function __construct(string $eos_id)
    {
        $this->InfoType = "CurrentQuests";
        parent::__construct($eos_id);
    }
}
