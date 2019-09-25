<?php
namespace Jelmergu\Calendar;

spl_autoload_register(function (string $fullClass) {
    $classes = [
        "Jelmergu\Calendar\AbstractCalendarEvent" => "src/AbstractCalendarEvent.php",
        "Jelmergu\Calendar\AbstractCalendarEvent" => "src/AbstractCalendarEvent.php",
        "Jelmergu\Calendar\CalendarICal"          => "src/CalendarICal.php",
        "Jelmergu\Calendar\CalendarEvent"         => "src/CalendarEvent.php",
        "Jelmergu\Calendar\DateIntervalExtended"  => "src/DateIntervalExtended.php",
        "Jelmergu\Calendar\ICalendarEvent"        => "src/ICalendarEvent.php",
    ];

    if (key_exists($fullClass, $classes)) {
        include_once $classes[$fullClass];
        spl_autoload($fullClass);
    }
});