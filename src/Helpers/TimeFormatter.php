<?php

namespace QuestApi\Helpers;

class TimeFormatter
{
    public static function secsToStr(int $secs): string
    {
        $parts = [];

        if ($secs >= 86400) {
            $days = floor($secs / 86400);
            $secs %= 86400;
            $parts[] = sprintf('%d day%s', $days, $days > 1 ? 's' : '');
        }

        if ($secs >= 3600) {
            $hours = floor($secs / 3600);
            $secs %= 3600;
            $parts[] = sprintf('%d hour%s', $hours, $hours > 1 ? 's' : '');
        }

        if ($secs >= 60) {
            $minutes = floor($secs / 60);
            $secs %= 60;
            $parts[] = sprintf('%d minute%s', $minutes, $minutes > 1 ? 's' : '');
        }

        if ($secs > 0) {
            $parts[] = sprintf('%d second%s', $secs, $secs > 1 ? 's' : '');
        } else {
            $parts[] = '0 seconds';
        }

        return implode(', ', $parts);
    }
}
