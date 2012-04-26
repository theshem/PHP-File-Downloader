<?php
/*
+ ------------------------------------------------------------------------------ +
|	PHP File Downloader
|
|	Copyright (C) 2012  Hashem Qolami
|	
|	This program is free software: you can redistribute it and/or modify
|	it under the terms of the GNU General Public License as published by
|	the Free Software Foundation, either version 3 of the License, or
|	any later version.
|	
|	This program is distributed in the hope that it will be useful,
|	but WITHOUT ANY WARRANTY; without even the implied warranty of
|	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
|	
|	Released under the terms and conditions of the
|	GNU General Public License (http://www.gnu.org/licenses/gpl.html).
|
|	$URL: https://github.com/qolami/PHP-File-Downloader $
|	$Version: 1.3 $
|	$Author: Hashem Qolami $
+ ------------------------------------------------------------------------------ +
*/

define("CORE_INIT", 1);

// Direct mode, supporting resume and downloading from file direct address.
define("DIRECT_GET", false);

// temporary URLs system for "Indirect mode".
define("TMP_ADDRS", false);

// temporary URLs file name.
define("TMP_ADDRS_FILE", 'tmp_addrs.json');

// temporary file max age in "seconds".
define("TMP_AGE_DURATION", 60*60*24);	// 24 hours

// temporary URLs duration in "seconds".
define("TMP_URL_DURATION", 60*60*3);	// 3 hours

// Prevent hotlinking.
define("REFERRER_LIMIT", false);

// control logging system.
define("LOG_DOWNLOADS", true);

// log file name.
define("LOG_FILE", 'download.log');

// The folder where you keep all files for download.
define("RELEASE_DIR", './releases');

// Version Separator character, used before version number to make understanable it for the program.
define("VER_SEP", '_');

// set delay for downloading, if the direct mode was enabled.
define("DELAY", 3);	// in seconds.

require_once("./module.php");

// If REFERRER_LIMIT is enabled, downloading will be allowed when referrer exists in $allowed_referrers
// (Example: example.com), NOT case-sensitive.
$allowed_referrers = array(
	'localhost',	// allow local requests
);

// valid type,MIME of the released files.
$valid_types = array(
	'zip'	=> 'application/zip',
	'rar'	=> 'application/x-rar-compressed',
	'gz'	=> 'application/x-gzip',
	'bz2'	=> 'application/x-bzip2',
	'7z'	=> 'application/x-7z-compressed'
);

////////////////////////////////////////////////////////////////////////////
/////////////////////////////   Main Section   /////////////////////////////

$version = $_GET['ver'] or die("You have to send <strong>project version</strong> by <strong>GET</strong> method, like: <strong>?ver=...</strong>");

$referrer = get_referrer();
$ip = get_ip();

if(!!REFERRER_LIMIT){
	check_referrer($referrer, $allowed_referrers) or die("Access Denied!");
}

// RUN forever BABY :D
@set_time_limit(0);
@ini_set("max_execution_time", 0);

$file = get_release_address(release_array(RELEASE_DIR, $valid_types), $version, VER_SEP) or die("Your requested file <strong>NOT</strong> found!");
$filename = basename($file);
$mime = $valid_types[strtolower(substr(strrchr($filename, '.'), 1))];


///////////////////////// set temporary url /////////////////////////////
if(!!TMP_ADDRS && !DIRECT_GET && (!isset($_GET['hash']) || empty($_GET['hash']))){
	$time = time();
	$tmp_id = hash('sha1', $time);
	
	$temp_str = @file_get_contents(TMP_ADDRS_FILE);
	$temp_arr = @json_decode($temp_str, true);
	
	$temp_arr = update_temp_file($temp_arr, TMP_AGE_DURATION);

	$temp_arr[$tmp_id] = array(
		'mktime'	=>	$time,
		'filename'	=>	$filename
	);
	
	@file_put_contents(TMP_ADDRS_FILE, json_encode($temp_arr));

	header("Location: $_SERVER[REQUEST_URI]&hash=$tmp_id");
	exit();
}
///////////////////////////////////////////////////////////////////////

if(!!TMP_ADDRS && !DIRECT_GET){
	check_url_valid($_GET['hash'], TMP_URL_DURATION) or die('Your request URL has been Expired!');
}

header("Pragma: public");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Cache-Control: public");
header("Content-Description: File Transfer");
header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
header("Content-type: $mime");
header('Content-length: '.filesize($file));
header("Content-Transfer-Encoding: binary");
header('Content-disposition: attachment; filename="'.$filename.'"');

if(!!DIRECT_GET){
	sleep(DELAY);
	header("Location: $file");
}else{
	$h = @fopen($file, "rb") or die('Your requested file <strong>NOT</strong> found!');
	while(!feof($h)) {
		print(fread($h, 8192));
		flush();
		if(connection_status()!=0) {
			@fclose($file);
			exit();
		}
	}
	@fclose($h);
}

if(!!LOG_DOWNLOADS){
	log_download_info(LOG_FILE, array(
		'date'		=>	date("m/d/Y H:i:s"),
		'ip'		=>	$ip,
		'referrer'	=>	$referrer,
		'filename'	=>	$filename
	));
}
?>