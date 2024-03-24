<?php

namespace QuestApi\ResponseObjects;

use QuestApi\ResponseObjects\ResponseObject;

class CompletedQuestsResponse extends ResponseObject
{
    public $CompletedQuests;
    public function __construct(string $eos_id)
    {
        $this->InfoType = "CompletedQuests";
        parent::__construct($eos_id);
    }
}
