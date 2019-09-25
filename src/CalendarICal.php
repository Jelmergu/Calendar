<?php
namespace Jelmergu\Calendar;

class CalendarICal
{
    protected $meta = [
        "version"          => "2.0",
        "calscale"         => "GREGORIAN",
        "method"           => "PUBLISH",
        "prodid"           => "Jelmergu",
        "name"             => null,
        "refresh-interval" => null,
        'x-wr-calname'     => null,
        'x-wr-timezone'    => "Europe/Amsterdam",
        'x-wr-caldesc'     => null,
    ],
        /**
         * @var ICalendarEvent[] $events
         */
        $events = [];

    public function __construct(string $name = "Default Calendar", string $description = null)
    {
        $this->meta['x-wr-calname'] = &$this->meta['name'];
        $this->meta['name']         = $name;
        $this->meta['x-wr-caldesc'] = is_null($description) === false ? self::chopLength($description) : null;
    }

    public function draw() : string
    {

        if (count($this->events) === 0) {
            return "";
        }
        $output = "BEGIN:VCALENDAR\r\n";
        foreach ($this->meta as $key => $value) {
            if (is_null($value)) {
                continue;
            }
            $output .= strtoupper($key).":".$value."\r\n";
        }

        return $output.
               $this->parseEvents().
               "END:VCALENDAR";
    }

    public function addCalendarEvent(ICalendarEvent &$calendarEvent)
    {
        $this->events[] = $calendarEvent;
    }

    public static function escapeText(string $input) : string
    {
        $output = str_replace(",", "\,", $input);
        $output = str_replace(";", "\;", $output);
        $output = str_replace(":", "\:", $output);
        $output = str_replace("\n", '\n', $output);

        return $output;
    }

    public static function chopLength(string $input) : string
    {
        if (strlen($input) <= 75) {
            return $input;
        }

        $split = str_split($input, 75);

        for ($i = 0; $i < count($split) - 1; $i++) {
            $split[$i] .= "\r\n";
        }

        return implode($split);
    }

    public function getName() : string
    {
        return $this->meta['name'];
    }

    protected function parseEvents() : string
    {
        $output = "";
        /**
         * @var ICalendarEvent $event
         */
        foreach ($this->events as $event) {
            $output .= $event->toiCal();
        }

        return $output;
    }
}