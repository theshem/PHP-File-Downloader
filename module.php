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

defined("CORE_INIT") or die("Access Denied!");

function multi_array_search($obj, $parent_arr){ 
	foreach($parent_arr as $key => $val){ 
		if(is_array($val)) {
			if(in_array($obj, $val)) return $key;
		}else{
			if($val == $obj) return $key;
		}
	} 
	return 'not found!'; 
} 

function release_array($directory, $vFileTyp){
	is_dir($directory) or die('folder not found!');
	$dir = @opendir($directory);
	$i = 0;
	while(($file = @readdir($dir)) !== false){
		$filetype = strtolower(substr(strrchr($file, '.'), 1));	// strtolower(array_pop(explode('.', $file)));
		if(array_key_exists($filetype, $vFileTyp)) {
			$files[$i][] = $directory.'/'.$file;
			$files[$i][] = filemtime($files[$i][0]);
			$i++;
		}
	}
	@closedir($dir);
	return $files;
}

function get_release_address($arr, $ver, $ver_separator=NULL){
	isset($ver_separator) or $ver_separator = '_';
	
	if($ver == 'latest'){
		for($i=0; $i<count($arr); $i++){
			$modtime[] = $arr[$i][1];
		}
		return $arr[multi_array_search(max($modtime), $arr)][0];
	}else{
		for($i=0; $i<count($arr); $i++){
			if(preg_match("#^(.+)".$ver_separator.$ver."[^0-9]*(\.[a-z7]{2,})$#i", basename($arr[$i][0]))) return $arr[$i][0];
		}
		return false;
	}
}

function get_referrer(){
	return isset($_SERVER['HTTP_REFERER'])?strtolower($_SERVER['HTTP_REFERER']):NULL;
}

function get_ip(){
	return isset($_SERVER['HTTP_X_FORWARDED_FOR'])?$_SERVER['HTTP_X_FORWARDED_FOR']:$_SERVER['REMOTE_ADDR'];
}

function check_referrer($ref, $refs){
	if(!isset($ref) || strlen($ref)<1) return false;
	foreach($refs as $val){
		if(preg_match("#".trim($val)."#i", $ref)) return 1;
	}
	return false;
}

function log_download_info($log_file, $arr){
	if($handler = @fopen($log_file, 'a+')){
		@fputs($handler, implode("  ", $arr)."\n");
		@fclose($handler);
	}
}

function check_url_valid($hash, $duration){
	$current_time = time();
	$arr = @json_decode(file_get_contents(TMP_ADDRS_FILE), true);
	
	if(!array_key_exists($hash, $arr)) return false;
	if(($current_time - floatval($arr[$hash]['mktime'])) < $duration) return 1;
	return false;
}

function update_temp_file($arr, $duration){
	if(!is_array($arr)) $arr = array();
	$current_time = time();
	
	if(!array_key_exists('creation', $arr)){
		$arr['creation'] = $current_time;
	}else{
		if(($current_time - floatval($arr['creation'])) > $duration){
			unset($arr);
			$arr = array();
		}
	}
	return $arr;
}
?>