<?php
namespace QuestApi\Helpers;

use QuestApi\Controllers\ConfigController;
use QuestApi\Controllers\DatabaseController;

class GetDiscordLinked
{
    public bool $discordLinked;

    public function __construct(string $eos_id)
    {
        $this->getDiscordLinked($eos_id);
    }

    public function getDiscordLinked(string $eos_id): bool
    {
        $config = (new ConfigController)->get();
        $discordLinkEnabled = (isset($config['discordlinkFeature'])) ? $config['discordlinkFeature'] : false;
    
        if ( $discordLinkEnabled === false) {
            $this->discordLinked = true;
            return $this->discordLinked;
        }
    
        $db = DatabaseController::getConnection();
        $discordLink = $db->queryFirstRow("SELECT * FROM discordlink WHERE eos_id = %s", $eos_id);
    
        if ($discordLink === null || $discordLink['discord_id'] === null) {
            $this->discordLinked = false;
        } else {
            $this->discordLinked = true;
        }
    
        return $this->discordLinked;
    }
}