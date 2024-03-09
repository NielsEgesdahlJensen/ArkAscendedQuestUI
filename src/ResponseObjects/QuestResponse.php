<?php
namespace QuestApi\ResponseObjects;
use QuestApi\ResponseObjects\ResponseObject;

class QuestResponse extends ResponseObject {
    public $Quest;
    public function __construct(string $eos_id) {
        $this->InfoType = "Quest";
        parent::__construct($eos_id);
    }
}