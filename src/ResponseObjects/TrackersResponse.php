<?php
namespace QuestApi\ResponseObjects;
use QuestApi\ResponseObjects\ResponseObject;

class TrackersResponse extends ResponseObject {
    public $Trackers;
    public function __construct(string $eos_id) {
        $this->InfoType = "Trackers";
        parent::__construct($eos_id);
    }
}