<?php

namespace Jelmergu\Calendar;

use PHPUnit\Framework\TestCase;

class CalendarICalTest extends TestCase
{
    /**
     * @var CalendarICal $calendar

     */
    private $calendar,
        $prophecyEvent;


    public function testIfGetNameReturnsString()
    {
        $this->assertEquals(
            "Default Calendar",
            (new CalendarICal())->getName()
        );
    }

    public function testIfDrawDrawsCorrectCalendar()
    {
        $event = $this->prophesize(CalendarEventMock::class);
        $event->toiCal()->willReturn((new CalendarEventMock())->toiCal());
        $event = $event->reveal();
        $this->calendar->addCalendarEvent($event);

        $this->assertEquals(
            "BEGIN:VCALENDAR\r\nVERSION:2.0\r\nCALSCALE:GREGORIAN\r\nMETHOD:PUBLISH\r\nPRODID:Jelmergu\r\nNAME:Default Calendar\r\nX-WR-CALNAME:Default Calendar\r\nX-WR-TIMEZONE:Europe/Amsterdam\r\n".
            "BEGIN:VEVENT\r\nSUMMARY:Foo\r\nUID:eaf948e8b93262448ccf0d0ff6b01be827a58c008555ea046f2683da62d4@packages.jel".
            "\r\nDTSTAMP:20170919T000000\r\nDTSTART:20170919T100000\r\nDEND:20190924T191330\r\nEND:VEVENT\r\nEND:VCALENDAR",
            $this->calendar->draw()
        );
    }

    public function testIfDrawDrawsNothingWhenNoEventsAreAdded()
    {
        $this->assertEquals(
            "",
            (new CalendarICal())->draw()
        );
    }

    public function testIfCommaGetsEscaped()
    {
        $this->assertEquals(
            "Hello\, this is a string",
            CalendarICal::escapeText("Hello, this is a string")
        );
    }

    public function testIfSemicolonGetsEscaped()
    {
        $this->assertEquals(
            "Hello\; this is a string",
            CalendarICal::escapeText("Hello; this is a string")
        );
    }

    public function testIfColonGetsEscaped()
    {
        $this->assertEquals(
            "Hello\: this is a string",
            CalendarICal::escapeText("Hello: this is a string")
        );
    }

    public function testIfNewlineGetsEscaped()
    {
        $this->assertEquals(
            'Hello\n                this is a string',
            CalendarICal::escapeText("Hello
                this is a string")
        );
    }

    public function testIfLongStringGetsChoppedAtCorrectLength()
    {
        $this->assertEquals(
            "This is quite a long string. I need to put some rubbish in here presumably \r\nthis is long enough",
            CalendarICal::chopLength("This is quite a long string. I need to put some rubbish in here presumably this is long enough")
        );
    }

    public function testIfShortStringDoesntGetChopped()
    {
        $this->assertEquals(
            "This is a short string",
            CalendarICal::chopLength("This is a short string")
        );
    }

    protected function setUp() : void
    {
        $this->calendar  = new CalendarICal();
        $event = $this->prophesize(CalendarEventMock::class);
        $event->toiCal()->willReturn((new CalendarEventMock())->toiCal());

        $this->prophecyEvent = $event;
    }
}

class CalendarEventMock implements ICalendarEvent
{
    public function toiCal()
    {
        return "BEGIN:VEVENT\r\nSUMMARY:Foo\r\nUID:eaf948e8b93262448ccf0d0ff6b01be827a58c008555ea046f2683da62d4@packages.jel".
        "\r\nDTSTAMP:20170919T000000\r\nDTSTART:20170919T100000\r\nDEND:20190924T191330\r\nEND:VEVENT\r\n";
    }
}