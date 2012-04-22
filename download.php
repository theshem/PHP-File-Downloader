<?php
define("_CORE", 1);

require_once("./module.php");

$version = $_GET['ver'] or die("You have to send <strong>project version</strong> by <strong>GET</strong> method, like: <strong>?ver=...</strong>");

define("VER_SEP", '_');

$valid_types = array(
	'zip'	=> 'application/zip',
	'rar'	=> 'application/x-rar-compressed',
	'gz'	=> 'application/x-gzip',
	'bz2'	=> 'application/x-bzip2',
	'7z'	=> 'application/x-7z-compressed'
);

// RUN forever BABY :D
set_time_limit(0);

$file = get_release_address(release_array('./releases', $valid_types), $version, VER_SEP) or die("Your requested file <strong>NOT</strong> found!");

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
header('Content-disposition: attachment; filename="'.basename($file).'"');

readfile($file);
?>