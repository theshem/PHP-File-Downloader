<?php
define("_CORE", 1);

require_once("./module.php");

$version = $_GET['ver'] or die("You have to send <strong>project version</strong> by <strong>GET</strong> method, like: <strong>?ver=...</strong>");

// Direct mode, supporting resume and downloading from file direct address.
define("DIRECT_GET", false);

// Prevent hotlinking.
define("REFERRER_LIMIT", true);

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

$referrer = get_referrer();
$ip = get_ip();

if(!!REFERRER_LIMIT){
	check_referrer($allowed_referrers) or die("Access Denied!");
}

// RUN forever BABY :D
set_time_limit(0);

$file = get_release_address(release_array(RELEASE_DIR, $valid_types), $version, VER_SEP) or die("Your requested file <strong>NOT</strong> found!");
$filename = basename($file);

$mime = $valid_types[strtolower(substr(strrchr(basename($file), '.'), 1))];

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
	readfile($file);
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