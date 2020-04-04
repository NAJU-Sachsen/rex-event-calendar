<?php

class naju_event_calendar
{
    public static $DB_DATE_FMT = 'Y-m-d';

    public static function hasExtraInfos($event)
    {
        return $event['event_location']
            || $event['event_target_group']
            || $event['event_price']
            || $event['event_price_reduced']
            || $event['event_registration'];

    }
}
