<?php
/**
 * Copyright 2015-2016 Twenty Four Colors @web twentyfourcolors.com
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

/**
 * Backup APP PHP class
 *
 * @package backup.app
 * @version 1.0
 * @author Hugo Robles <hugorobles@twentyfourcolors.com>
 */

/*
 *
 * HOWTO GUIDE
 *
 * -
 * --
 * --- IMPORTANT
 * -
 * The directory structure where the app works is from the directory where your locate.
 * Recommended place in the main directory where the projects are on which you want to perform the backup
 *
 *
 *
 * -
 * --
 * --- BACKUP
 * -
 *
 * START BACKUP
 *-> $backup = new BackupThis('folder','/cache'); | OPTIONS 'folder' or 'database'
 *---> For option 'database', is need declared mysql database settings. Example: $backup = new BackupThis('database','YOUR DATABASE NAME','YOUR HOST','YOUR USER','YOUR PASS')
 *
 * AFTER, ESTABLISH DAY AND TIME TO EXECUTE
 *-> $backup->action('cron','Wednesday','00:00'); | OPTIONS 'cron' or 'now', 'english day' or 'array('day','day')'
 *---> Option 'cron', execute backup when is the configuration time. Example $backup->action('cron','Wednesday','00:00');
 *---> Option 'now', execute immediately backup regardless of the time set. Example: $backup->action('now','Wednesday','00:00');
 *---> Option 'Monday', specific english name day to backup execute,  php date("d"). Example: $backup->action('now','Monday','00:00');
 *---> Options 'Various Days', specific various english name days with array(). Example: $backup->action('now',array('Monday','Friday','Sunday'),'00:00');
 *
 *
 * -
 * --
 * --- SETTINGS
 * -
 *
 *
 * SET LIVE BACKUP
 *-> $backup->set_live('7'); Live to backup. Default: 30 days
 *---> Option '7', set live backup weekly.
 *
 * SET PERSONAL NAME IN BACKUP FILE
 *-> $backup->set_name('My First Backup'); Additional name to file backup
 *
 * INCLUDE MORE DIRECTORIES IN THE BACKUP
 *-> $backup->include_folder('/config');
 *
 * INCLUDE DATABASE INSIDE BACKUP
 *-> $backup->include_database('YOUR HOST','YOUR USER','YOUR PASS','YOUR DATABASE');
 *
 * UPLOAD BACKUP FILE TO FTP SERVER
 *-> $backup->save_ftp('YOUR FTP HOST','YOUR FTP USER','YOUR FTP PASS','PATH IN FTP FOR SAVE BACKUP');
 *---> Example: $backup->save_ftp('twentyfourcolors.net','test_user','test_pass','/var/www/');
 *---> Is this option is disable, the backup file save in 'save/' local folder and database backup save in 'save-mysql/' local folder
 *
 * SEND EMAIL WITH BACKUP LOG
 *-> $backup->sendmail('YOUR EMAIL DIRECTION');
 *---> Example: $backup->sendmail('hugorobles@twentyfourcolors.com');
 *
 */

/* MAIN CLASS */
require_once("inc/backup.class.php");

/* Basic to backup */
/*$app = new BackupThis('folder','/magnificent/tools','','','');*/
$app = new BackupThis('database','backuptesting','mmreservas.twentyfourcolors.net','root','root');
/*$app->include_database('mmreservas.twentyfourcolors.net','root','root','backuptesting');*/
/*$app->include_folder('/magnificent/documentation');*/
$app->save_ftp('ftp.cluster005.ovh.net','rosamaridb','8Dn0267Kg','/');
$app->action('now','Friday','00:00');


/* Backup with options extra */
/*$app = new BackupThis('folder','/cache');
$app->set_live('7');
$app->set_name('PERSONALNAME_BACKUP');
$app->include_folder('/config');
$app->include_database('localhost','test_user','test_pass','backuptesting');
$app->save_ftp('twentyfourcolors.net','test_user','test_pass','/var/www/');
$app->sendmail('hugorobles@twentyfourcolors.com');
$app->action('cron','Wednesday','00:00');*/

/* Only Database and FTP Upload Backup */
/*$app = new BackupThis('database','backuptesting','localhost','test_user','test_pass');
$app->save_ftp('twentyfourcolors.net','test_user','test_pass','/var/www/');
$app->action('cron','Thursday','00:00');*/




