<?php
namespace QuestApi\ResponseObjects;

class ResponseObject {
    public $ModName;
    public $EOS_ID;
    public $InfoType;

    public function __construct($EOS_ID) {
        $this->ModName = 'LethalQuestsUI';
        $this->EOS_ID = $EOS_ID;
    }
}