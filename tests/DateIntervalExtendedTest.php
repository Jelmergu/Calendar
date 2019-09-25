<?php
namespace Jelmergu\Calendar;

use PHPUnit\Framework\TestCase;

class DateIntervalExtendedTest extends TestCase
{
    public function testFormatReturnsPeriod() {
        $this->assertEquals(
            "P2DT0S",
        (new DateIntervalExtended("P2D"))->format("%P")
        );
    }

    public function testFormatStillAllowsParent() {
        $this->assertEquals(
            "02",
            (new DateIntervalExtended("P2D"))->format("%D")
        );
    }

    public function testConvertDateIntervalToDateIntervalExtended() {
        $interval = new \DateInterval("P1Y3M2DT5H6M7S");
        $this->assertEquals(
            "P1Y3M2DT5H6M7S",
            DateIntervalExtended::createFromDateInterval($interval)->format("%P")
        );
    }
}