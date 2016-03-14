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
 * Backup App Class
 *
 * @package backup.app
 * @version 1.0
 * @author Hugo Robles <hugorobles@twentyfourcolors.com>
 */


/* LIBRARIES */
require_once(__DIR__ . "/../lib/time.now.php");
require_once(__DIR__ . "/../lib/vendor/autoload.php");
use FtpPhp\FtpClient;
use FtpPhp\FtpException;

/* Start Login */
LogMore::open('BackupAPP');

/* FUNCTIONS */
require("autorize.php");


class BackupThis
{

    // INIT PUBLIC VARIABLES
    private $get_live;
    private $mysql_conn;
    private $name_file;
    private $name_file_path;
    private $name_file_database;
    private $name_file_database_path;
    private $email_log;
    private $email_info;
    private $include_dir;
    private $folder_content;
    private $path;
    private $base;
    private $baseapp;
    private $type;
    private $get_name;
    private $ftp_conn;


    function __construct($type, $path_backup, $host, $user, $pass)
    {
        $this->type = $type; /* Mode to backup */
        $this->path = $path_backup; /* Folder path to backup */
        $this->base = __DIR__;
        $this->baseapp = dirname(__DIR__).'/';

        if ($type == 'database') {
            $this->mysql_conn = array(
                'host' => $host,
                'user' => $user,
                'pass' => $pass,
                'database' => $path_backup,
            );
        }
    }

    // Set live to backup
    public function set_live($time)
    {
        LogMore::debug('Set backup live to "' . $time . '"');
        $this->get_live = $time;
    }

    // Set extension name
    public function set_name($name)
    {
        LogMore::debug('Set backup name to "' . $name . '"');
        $this->get_name = str_replace(' ', '-', $name);
    }

    // Include Mysql Database inside Folder Backup
    public function include_database($host, $user, $pass, $database)
    {
        $this->mysql_conn = array(
            'host' => $host,
            'user' => $user,
            'pass' => $pass,
            'database' => $database,
        );
    }

    /* Include more folder inside some backup */
    public function include_folder($path_folder)
    {
        LogMore::debug('Including directory ' . $path_folder);

        if (empty($this->include_dir)) {
            $this->include_dir = array($path_folder);
        } else {
            array_push($this->include_dir, $path_folder);
        }
    }

    /* Backup Ftp */
    public function save_ftp($host, $user, $pass, $path)
    {
        LogMore::debug('Add FTP ' . $host . ' for upload backup');
        $this->ftp_conn = array(
            'host' => $host,
            'user' => $user,
            'pass' => $pass,
            'path' => $path,
        );

    }

    /* Email Log */
    public function sendmail($email)
    {
        LogMore::debug('Activated send mail log to' . $email);
        $this->email_log = $email;
    }

    /* Send Mail Log */
    private function sendlog($email)
    {
        LogMore::debug('Sending log to' . $email);

        require_once(__DIR__ . "../templates/mail/basic.php");

        $mail = new PHPMailer;

        $mail->setFrom('backup@mediamarkt.es', 'Backup App System');
        $mail->addAddress($email, $this->email_info['name']);     // Add a recipient
        $mail->isHTML(true);                                  // Set email format to HTML

        $mail->Subject = 'Backup App - System Log';
        $mail->Body = $email_template;

        if (!$mail->send()) {
            LogMore::debug('Email send to successfully');
            return false;
        } else {
            LogMore::debug('There was a problem while send mail');
            return true;

        }
    }

    /* Delete directory not empty */
    private function deleteDir($dir)
    {
        if (!file_exists($dir)) {
            return true;
        }
        if (!is_dir($dir)) {
            return unlink($dir);
        }
        foreach (scandir($dir) as $item) {
            if ($item == '.' || $item == '..') {
                continue;
            }

            if (!$this->deleteDir($dir . DIRECTORY_SEPARATOR . $item)) {
                return false;
            }
        }
        return rmdir($dir);
    }

    /* Backup Mysql */
    private function backup_mysql($host, $user, $pass, $database)
    {
        LogMore::debug('Include database "' . $database . '"');

        if ($host == 'localhost') {
            $host = '127.0.0.1';
        }
        $filename_mysql = $this->make_name('database', $database);
        $filename_mysql_path = $this->baseapp.$filename_mysql;

        $dump = new MySQLDump(new mysqli($host, $user, $pass, $database));

        $dump->save($filename_mysql_path);
        if (file_exists($filename_mysql_path)) {
            LogMore::debug('Backup Database ' . $database . ' Successfully');

        }
        /* Compress database backup */
        $zip = new ZipArchive();
        $zip->open($filename_mysql_path . '.zip', ZipArchive::OVERWRITE | ZipArchive::CREATE);
        $zip->addFile($filename_mysql_path);
        $zip->close();
        unlink($filename_mysql_path);
        $this->name_file_database = $filename_mysql . '.zip';
        $this->name_file_database_path = $filename_mysql_path.'.zip';

    }

    /* FTP Upload */
    private function file_upload($file_upload)
    {
        LogMore::debug('Starting to upload backup...');

        $ftp_conn = new FtpClient("ftp://" . $this->ftp_conn['user'] . ":" . $this->ftp_conn['pass'] . "@" . $this->ftp_conn['host'] . "/" . $this->ftp_conn['path'] . "");
        if ($ftp_conn->put($this->ftp_conn['path'] . $this->name_file, $file_upload, FtpClient::BINARY)) {
            LogMore::debug('Backup ' . $this->name_file . ' uploaded successfully in ' . $this->ftp_conn['host']);
            unlink($file_upload);
        } else {
            LogMore::debug('There was a problem while uploading ' . $this->name_file);
        }
        $ftp_conn->close();
    }

    /* Backup Folder */
    private function backup_folder($path_folder, $type)
    {
        LogMore::debug('Include folder "' . $path_folder . '"');

        /* Make backup file name */
        if ($type == 'main') {
            $this_zip = $this->make_name($this->type, $path_folder);
            $this->name_file = $this_zip;
            $name_file_path = $this->baseapp.$this_zip;
            $this->name_file_path = $name_file_path;
        } else if ($type == 'include') {
            $this_zip = $this->make_name('folder', $path_folder);
        }

        $zip = new ZipArchiveEx();
        $zip->open($name_file_path, ZipArchive::OVERWRITE | ZipArchive::CREATE);

        # Add whole directory including contents:
        /*$zip->excludeDir(basename($path_www).$exclude_directory_files);*/
        $zip->addDir(dirname(dirname($this->base)) . $path_folder);
        $zip->close();

        /* Return name include folder*/
        if ($type == 'include') {
            return $this_zip;
        }

        LogMore::debug('Include directory ' . $path_folder . ' successfully');
    }

    /* Compress Folder */
    private function compress_folder($path_folder)
    {
        $zip = new ZipArchiveEx();
        $zip->open($this->name_file_path, ZipArchive::OVERWRITE | ZipArchive::CREATE);
        $zip->addDir($path_folder);
        $zip->close();
    }

    /* Make folder for more content */
    private function make_folder($name_folder)
    {
        $folder_content = $this->baseapp.str_replace('.zip', '', $name_folder);
        $this->folder_content = $folder_content;
        mkdir($folder_content);
    }

    /* Make name to backup file */
    private function make_name($type, $path_backup)
    {
        $time_live = $this->get_live;

        if (!empty($this->get_name)) {
            $personal_name = $this->get_name . '_';
        }

        if (!empty($time_live)) {
            if ($time_live == '7') {
                $filename_live = 'LIVE-' . date('l'); /* Week day in text -> Monday, Friday, Saturday */
            }
        } else {
            $filename_live = 'LIVE-' . date('d'); /* Month day in two digits -> 01 - 31  */
        }
        if ($type == 'folder') {
            $path_backup = explode('/', $path_backup);
            return $personal_name . array_pop($path_backup) . "_BACKUP_" . strtoupper($type) . "_" . $filename_live . ".zip";
        } elseif ($type == 'database') {
            return $personal_name . $path_backup . "_BACKUP_" . strtoupper($type) . "_" . $filename_live . ".sql";
        }

    }

    /* Action Backup */
    public function action($mode, $days, $time)
    {

        /* Autorize backup */
        if ($mode == 'cron') {
            if (is_time($days, $time)) {
                $autorize = 1;
            }
        } elseif ($mode == 'now') {
            $autorize = 1;
        }

        if (isset($autorize)) {

            LogMore::debug('Starting BackupAPP');

            /* Do backup Folder or Mysql */
            if ($this->type == 'folder') {

                /* Compress Folder Action */
                $this->backup_folder($this->path, 'main');

                /* Include extra folders or database */
                if (!empty($this->include_dir) or !empty($this->mysql_conn)) {

                    $this->make_folder($this->name_file);

                    /* Include extra folders */
                    if (!empty($this->include_dir)) {
                        foreach ($this->include_dir as $dir) {
                            $name_file = $this->backup_folder($dir, 'include');
                            rename($this->baseapp.$name_file, $this->folder_content . '/' . $name_file);
                        }
                    }

                    /* Include database */
                    if (!empty($this->mysql_conn)) {
                        $this->backup_mysql($this->mysql_conn['host'], $this->mysql_conn['user'], $this->mysql_conn['pass'], $this->mysql_conn['database']);
                        rename($this->baseapp.$this->name_file_database, $this->folder_content . '/' . $this->name_file_database);
                    }

                    /* Move main backup folder to content folder */
                    rename($this->name_file_path,  $this->folder_content . '/' . $this->name_file);

                    /* Compress Content folder and delete */
                    $this->compress_folder($this->folder_content);
                    $this->deleteDir($this->folder_content);

                }

            } elseif ($this->type == 'database') {
                $this->backup_mysql($this->mysql_conn['host'], $this->mysql_conn['user'], $this->mysql_conn['pass'], $this->mysql_conn['database']);
                $this->name_file_path = $this->baseapp.$this->name_file_database;
                $this->name_file = $this->name_file_database;
            }

            /* Upload FTP or Save in Disk */
            if (empty($this->ftp_conn)) {

                if ($this->type == 'folder') {
                    LogMore::debug('Moving backup to directory "save"');

                    rename($this->name_file_path, $this->baseapp.'save/' . $this->name_file);
                } elseif ($this->type == 'database') {
                    LogMore::debug('Moving database backup to directory "save-mysql"');

                    rename($this->name_file_database_path, $this->baseapp.'save-mysql/' . $this->name_file_database);
                }

            } else {
                /* Function to upload */
                $this->file_upload($this->name_file_path);
            }

            /* Send Email Log */
            if (!empty($this->email_log)) {

                /* Email information */
                $this->email_info = array(
                    'mode' => $mode,
                    'day' => $days,
                    'time' => $time,
                    'email' => $this->email_log,
                    'name' => stristr($this->email_log, "@", true),
                    'name_file' => $this->name_file,
                    'name_file_database' => $this->name_file_database,
                    'type' => $this->type,
                    'folder' => $this->path,
                    'database' => $this->mysql_conn['database'],
                );
                $this->sendlog($this->email_log);
            }

            LogMore::debug('All BackupAPP process have completed successfully');
            LogMore::debug('Your backup is ' . $this->name_file);
            LogMore::debug('Thanks for use BackupAPP');
        }

    }
}