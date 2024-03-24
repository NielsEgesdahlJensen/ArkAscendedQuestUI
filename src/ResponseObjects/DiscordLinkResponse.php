<?php

namespace QuestApi\ResponseObjects;

use QuestApi\ResponseObjects\ResponseObject;

class DiscordLinkResponse extends ResponseObject
{
    public $error;
    public $ActivationCode;

    public function __construct($EOS_ID, $error = null)
    {
        parent::__construct($EOS_ID);
        if ($error)
            $this->error = $error;
        else
            unset($this->error);
        $this->InfoType = 'DiscordLink';
    }
}
