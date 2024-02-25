<?php
namespace QuestApi\ResponseObjects;
use QuestApi\ResponseObjects\ResponseObject;

class ErrorReponse extends ResponseObject{
    public $error;

    public function __construct($EOS_ID, $infoType, $error) {
        parent::__construct($EOS_ID);
        $this->error = $error;
        $this->InfoType = $infoType;
    }
}