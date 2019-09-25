<?php

namespace Jelmergu\Calendar;

use PHPUnit\Framework\TestCase;

class CalendarEventTest extends TestCase
{
    private $event;

    public function testSetStartDate()
    {
        $this->assertTrue(
            $this->event->setStartDate(new \DateTime("2019-01-02 00:00:00"))
        );
        $this->assertEquals(
            "2019-01-02 00:00:00",
            $this->event->getStartDate()
        );
    }

    public function testSetEndDate()
    {
        $this->assertTrue(
            $this->event->setEndDate(new \DateTime("2019-01-02 00:00:00"))
        );
        $this->assertEquals(
            "2019-01-02 00:00:00",
            $this->event->getEndDate()
        );
    }

    public function testSetDuration()
    {
        $this->event->setDuration(new DateIntervalExtended("P2D"));
        $this->assertEquals(
            "02",
            $this->event->getDuration()
        );
    }

    public function testSetDurationWhenEndDateIsSetFails()
    {
        $this->event->setEndDate(new \DateTime("2019-01-02 00:00:00"));

        $this->assertFalse($this->event->setDuration(new \DateInterval("P2D")));
    }

    public function testSetEndDateWhenDurationIsSetFails()
    {
        $this->event->setDuration(new \DateInterval("P2D"));

        $this->assertFalse($this->event->setEndDate(new \DateTime("2019-01-02 00:00:00")));
    }

    public function testSetLocation()
    {
        $this->event->setLocation("On earth");

        $this->assertEquals(
            "On earth",
            $this->event->getLocation()
        );
    }

    public function testSetDescription()
    {
        $this->event->setDescription("On earth");

        $this->assertEquals(
            "On earth",
            $this->event->getDescription()
        );
    }

    public function testPrintDurationAndEndDateWithEmptyValues()
    {
        $this->assertEquals(
            "",
            $this->event->printDurationOrEndDate()
        );
    }

    public function testPrintDurationAndEndDateWithDuration()
    {
        $this->event->setDuration(new DateIntervalExtended("P2D"));

        $this->assertEquals(
            "DURATION:P2DT0S\r\n",
            $this->event->printDurationOrEndDate()
        );
    }

    public function testPrintDurationAndEndDateWithEndDateOnUTC()
    {
        $this->event->setEndDate(new \DateTime("2019-09-09"));

        $this->assertEquals(
            "DEND:20190909T000000\r\n",
            $this->event->printDurationOrEndDate()
        );
    }

    public function testPrintDurationAndEndDateWithEndDateOffUTC()
    {
        $this->event->setEndDate(new \DateTime("2019-09-09", new \DateTimeZone("-0500")));

        $this->assertEquals(
            "DEND;TZID=-05:00:20190909T000000\r\n",
            $this->event->printDurationOrEndDate()
        );
    }

    public function testToIcal()
    {
        $this->assertEquals(
            "BEGIN:VEVENT\r\nSUMMARY:Test\r\nUID:something".
            "\r\nDTSTAMP:20190909T000000\r\nDTSTART:20190909T000000\r\nEND:VEVENT\r\n",
            (new CalendarEvent("Test", new \DateTime("2019-09-09"), new \DateTime("2019-09-09"), $uid = "something"))->toiCal()
        );
    }

    public function testToIcalWhenSummaryIsEmpty()
    {
        $this->event->unsetSummary();
        $this->expectExceptionMessage("Required field 'summary' is not specified");
        $this->event->toiCal();
    }

    public function testToIcalWhenStartDateIsEmpty()
    {
        $this->event->unsetStartDate();
        $this->expectExceptionMessage("Required field 'startDate' is not specified");
        $this->event->toiCal();
    }

    public function testToIcalWhenTimestampIsEmpty()
    {
        $this->event->unsetTimestamp();
        $this->expectExceptionMessage("Required field 'timestamp' is not specified");
        $this->event->toiCal();
    }


    protected function setUp() : void
    {
        $_SERVER["SERVER_NAME"] = "Test";
        $this->event            = new AbstractCalendarEventMock(
            "Test Event",
            new \DateTime("2019-01-01"),
            new \DateTime("2019-01-02")
        );
    }
}


class AbstractCalendarEventMock extends AbstractCalendarEvent
{
    public function unsetSummary(){
        $this->summary = null;
    }

    public function unsetStartDate()
    {
        $this->startDate = null;
    }

    public function unsetTimestamp() {
        $this->timestamp = null;
    }

    /**
     * @return mixed
     */
    public function getSummary()
    {
        return $this->summary;
    }

    /**
     * @return mixed
     */
    public function getUid()
    {
        return $this->uid;
    }

    /**
     * @return mixed
     */
    public function getStartDate()
    {
        return $this->startDate->format("Y-m-d H:i:s");;
    }

    /**
     * @return mixed
     */
    public function getTimestamp()
    {
        return $this->timestamp->format("Y-m-d H:i:s");;
    }

    /**
     * @return mixed
     */
    public function getDuration()
    {
        return $this->duration->format("%D");
    }

    /**
     * @return mixed
     */
    public function getEndDate()
    {
        return $this->endDate->format("Y-m-d H:i:s");
    }

    /**
     * @return mixed
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @return mixed
     */
    public function getOrganizer()
    {
        return $this->organizer;
    }

    /**
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @return mixed
     */
    public function getGeo()
    {
        return $this->geo;
    }

    /**
     * @return mixed
     */
    public function getTransparency()
    {
        return $this->transparency;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }


}