<?php

namespace Jelmergu\Calendar;

use DateInterval;
use DateTime;

abstract class AbstractCalendarEvent implements ICalendarEvent
{
    // required parameters
    /**
     * @var  $startDate DateTime
     * @var  $summary   string
     * @var  $timestamp DateTime
     * @var  $uid       string
     */
    protected
        $summary,
        $uid,
        $startDate,
        $timestamp;

    // optional parameters
    /**
     * @var  DateIntervalExtended $duration
     * @var  DateTime             $endDate
     * @var  string               $location
     * @var  string               $organizer
     * @var  string               $url
     * @var  string               $geo
     * @var  string               $transparency
     * @var  string               $description
     */
    protected
        $duration, // mutually exclusive with endDate
        $endDate, // mutually exclusive with duration
        $location,
        $organizer,
        $url,
        $geo,
        $transparency,
        $description;

    public function __construct($summary, DateTime $startDate, DateTime $timestamp, $uid = null)
    {
        $this->summary   = $summary;
        $this->startDate = $startDate;
        $this->timestamp = $timestamp;
        $this->uid       = $uid ?? $this->generateUID();
    }

    public function setStartDate(DateTime $date)
    {
        $this->startDate = $date;
    }

    /**
     * Sets the end date of the event
     * The end date WILL NOT be set if a duration is specified, as they MUST NOT occur together as specified in <a href="https://tools.ietf.org/html/rfc5545#section-3.6.1">RFC 5545</a>
     *
     * @param DateTime $date
     *
     * @return bool Returns false if the end date is not set, true otherwise
     */
    public function setEndDate(DateTime $date) : bool
    {
        if (is_object($this->duration) && is_a($this->duration, DateIntervalExtended::class)) {
            return false;
        } else {
            $this->endDate = $date;

            return true;
        }
    }

    /**
     * Sets the duration of the event.
     * The duration WILL NOT be set if a end date is specified, as they MUST NOT occur together as specified in <a href="https://tools.ietf.org/html/rfc5545#section-3.6.1">RFC 5545</a>
     *
     * @param DateInterval $duration
     *
     * @return bool Returns false if the duration is not set, true otherwise
     */
    public function setDuration(DateInterval $duration) : bool
    {
        if (is_object($this->endDate) && is_a($this->endDate, DateTime::class)) {
            return false;
        } else {
            $this->duration = is_a($duration, DateIntervalExtended::class) ?
                $duration :
                DateIntervalExtended::createFromDateInterval($duration);

            return true;
        }
    }

    public function setLocation(string $location)
    {
        $this->location = $location;
    }

    public function setDescription(string $description)
    {
        $this->description = $description;
    }

    public function toiCal() : string
    {
        $this->uid = $this->uid ?? $this->generateUid();
        if (empty($this->summary) || $this->summary == "") {
            throw new \Exception("Required field 'summary' is not specified");
        } elseif (!is_a($this->startDate, "DateTime")) {
            throw new \Exception("Required field 'startDate' is not specified");
        } elseif (!is_a($this->timestamp, "DateTime")) {
            throw new \Exception("Required field 'timestamp' is not specified");
        }

        $output =
            "BEGIN:VEVENT\r\n".
            CalendarICal::chopLength("SUMMARY:".CalendarICal::escapeText($this->summary))."\r\n".
            "UID:".$this->uid."\r\n".
            "DTSTAMP".$this->prepareTimestamp($this->timestamp)."\r\n".
            "DTSTART".$this->prepareTimestamp($this->startDate)."\r\n".
            $this->optionalToiCal().
            "END:VEVENT\r\n";

        return $output;
    }

    public function prepareTimestamp(DateTime $date){
        return ($date->format("e") == "UTC" ? "" : ";TZID=".$date->format("e")).":".$date->format("Ymd")."T".$date->format("His");
    }

    public function generateUID(int $length = 30)
    {

        $uid = bin2hex(openssl_random_pseudo_bytes($length));

        if ($_SERVER['SERVER_NAME']) {
            $uid .= "@".$_SERVER['SERVER_NAME'];
        }

        $this->uid = $uid;

        return $uid;
    }

    public function printDurationOrEndDate()
    {
        if ((empty($this->duration) && empty($this->endDate)) == true) {
            return "";
        }
            return is_a($this->duration, DateIntervalExtended::class) ?
                "DURATION:".$this->duration->format("%P")."\r\n" :
                "DEND".$this->prepareTimestamp($this->endDate)."\r\n";
    }

    protected function optionalToiCal()
    {
        $output = "";
        $output .=
            $this->printDurationOrEndDate().
            (!empty($this->url) ? CalendarICal::chopLength("URL:".CalendarICal::escapeText($this->url))."\r\n" : "").
            (!empty($this->transparency) ? CalendarICal::chopLength("TRANSP:".strtoupper($this->transparency))."\r\n" : "").
            (!empty($this->description) ? CalendarICal::chopLength("DESCRIPTION:".CalendarICal::escapeText($this->description))."\r\n" : "").
            (!empty($this->location) ? CalendarICal::chopLength("LOCATION:".CalendarICal::escapeText($this->location))."\r\n" : "");
        // $organizer,
        // $geo,
        return $output;
    }
}