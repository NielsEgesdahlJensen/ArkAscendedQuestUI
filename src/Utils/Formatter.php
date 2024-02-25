<?php
namespace QuestApi\Utils;
use QuestApi\Controllers\ConfigController;

class Formatter
{
    public static function createProgressString(array $dailyStats, array $requirements): string
    {
        $config = (new ConfigController())->get();
        $linebreaksInProgress = $config['linebreaksInProgress'];

        $requirementNames =  array_keys($requirements);
        $requirementValues = array_values($requirements);
        $progressArray = [];

        if ($dailyStats) {
            foreach ($requirementNames as $index => $value) {
                $requirement = preg_replace("/[^0-9]/", '', $requirementValues[$index]);
                $stat = self::statName($value);
                $statValue = ($dailyStats[$value] >= $requirement) ? $requirement : $dailyStats[$value];
                $progressArray[] = $stat . ": " . $statValue . "/" . $requirement;
            }

            $separator = $linebreaksInProgress ? "\n" : ", ";

            return implode($separator, $progressArray);
        }

        return 'Not Initialized';
    }

    public static function statName(string $stat): string
    {
        $config = (new ConfigController())->get();
        $trackedNameOverrides = $config['trackedNameOverrides'];
        $addSpacesToTrackedNames = $config['addSpacesToTrackedNames'];

        if(isset($trackedNameOverrides[$stat])) 
        $statName = $trackedNameOverrides[$stat];

        else if ($addSpacesToTrackedNames)
            $statName = preg_replace('/(?<!\b|\p{Lu})(?=\p{Lu})/', ' $0', $stat);

        else 
            $statName = $stat;

        return $statName;
    }

    public static function unixTimeToHuman(int $unixTime): string
    {
        $config = (new ConfigController())->get();

        if($unixTime === '0') return 'Timestamp missing!';
        $dt = new \DateTime( '@'.$unixTime );
        $dt -> setTimeZone( new \DateTimeZone( $config['timezone'] ) );
        return $dt -> format( 'd-m-Y H:i' );
    }
    
    public static function sanitizeDescription(string $descriptionString): string
    {
        return preg_replace("/\{[^}]+\}\//", "", $descriptionString);
    }
}