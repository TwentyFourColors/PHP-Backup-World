<?php
/* Check time and day to backups */

/* Global variables */
/* $date_hour_min -> Time now: Hour:Min (12:00) */
/* $date_day_text -> Time now: Day Text (Monday) */

/*
 *
 * -
 * --
 * --- OPTIONS
 * -
 *
 * Days
 * -> One Day -> Example: 'Monday'
 * -> Various days -> Example: array('Monday','Friday','Saturday')
 * -> All days -> '*'
 *
 * Hour and Min
 * -> One hour with min -> '04:00'
 *
 *
 * */

function is_time($days, $time)
{
    global $date_hour_min;
    global $date_day_text;

    if ($time == $date_hour_min) {

        if ($days != '*') {

            if (is_array($days)) {
                foreach ($days as $day) {
                    if ($day == $date_day_text) {
                        return true;
                    }
                }
            } else {

                if ($days == $date_day_text) {
                    return true;
                }
            }

        } else {
            return true;
        }
    } else {
        return false;
    }
}
