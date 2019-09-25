<?php
namespace Jelmergu\Calendar;

use \DateInterval;

class DateIntervalExtended extends DateInterval
{

    public function format($format)
    {
        if ($format == "%P") {
            return $this->toPeriod();
        }
        return parent::format($format);
    }

    protected function toPeriod()
    {
        $output = "P".
                  ($this->y > 0 ? $this->y."Y" : "").
                  ($this->m > 0 ? $this->m."M" : "").
                  ($this->d > 0 ? $this->d."D" : "").
                  "T".
                  ($this->h > 0 ? $this->h."H" : "").
                  ($this->i > 0 ? $this->i."M" : "").
                  ($this->s > 0 ? $this->s."S" : "0S");
        return $output;
    }

    public static function createFromDateInterval (DateInterval $interval) : DateIntervalExtended {

        $newInterval = new DateIntervalExtended("P0D");
        $newInterval->y = $interval->y;
        $newInterval->m = $interval->m;
        $newInterval->d = $interval->d;
        $newInterval->h = $interval->h;
        $newInterval->i = $interval->i;
        $newInterval->s = $interval->s;
        $newInterval->f = $interval->f;
        $newInterval->invert = $interval->invert;
        $newInterval->days = $interval->days;

        return $newInterval;
    }
}