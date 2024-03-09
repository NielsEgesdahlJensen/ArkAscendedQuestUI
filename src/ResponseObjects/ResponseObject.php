<?php
namespace QuestApi\ResponseObjects;

use QuestApi\Controllers\ConfigController;

class ResponseObject {
    public $ModName;
    public $EOS_ID;
    public $InfoType;

    public function __construct($EOS_ID) {
        $this->ModName = 'LethalQuestsUI';
        $this->EOS_ID = $EOS_ID;
        $config = (new ConfigController)->get();

        if ( isset($config['includeModname']) && $config['includeModname'] === false) {
            unset($this->ModName);
        }
    }
}