<?php
// Variables de entorno de horario
date_default_timezone_set('Europe/Paris');
$date_full = date('Y-m-d H:i:s');
$date_today = date('d/m/Y');
$date_hour_min = date('H:i');
$date_hour = date('H');
$date_year = date("Y");
$date_month = date("M");
$date_day = date("l");
$date_min = date("i");
$date_day_number = date("d");
$date_day_text = date("l");
$date_mysql = strtotime($date_full);
$summer_time = date('I');

// Identifica si es horario de verano
if ($summer_time == 1) {
	$summer_hour = 2;
}else{
	$summer_hour = 1;
}

// Cada cinco minutos
$date_0_5_min = substr($date_min, 1);
if ($date_0_5_min == '0' || $date_0_5_min == '5') {
	$every_five_min = 1;
}

// Cada diez minutos
$date_10_min = substr($date_min, 1);
if ($date_10_min == '0') {
	$every_ten_min = 1;
}

// Cada hora
$date_00_min = substr($date_hour_min, 2);
$date_00_min = str_replace(":","",$date_00_min);
if ($date_00_min == '00') {
	$every_hour = 1;
}

// Doce de la noche
if ($date_hour_min == "00:00") {
	$time_00 = 1;
}

// Doce de la mañana
if ($date_hour_min == "12:00") {
	$time_12 = 1;
}

// Una vez a la semana
if ($date_hour_min == "00:00" && $date_day == 'Sunday') {
	$every_week = 1;
}

// Primer día del mes pasado
$date_day_first_last_month = new DateTime();
$date_day_first_last_month->modify('first day of last month');
$date_day_first_last_month_format = $date_day_first_last_month->format('d/m/Y');

// Ultimo día del mes pasado
$date_last_day_last_month = new DateTime();
$date_last_day_last_month->modify('last day of last month');
$date_last_day_last_month_format = $date_last_day_last_month->format('d/m/Y');

// Primer día el mes
$date_first_day_this_month = new DateTime();
$date_first_day_this_month->modify('first day of this month');
$date_first_day_this_month_format = $date_first_day_this_month->format('d/m/Y');

if ($date_first_day_this_month_format == $date_today) {
	$first_day_month = 1;
}
