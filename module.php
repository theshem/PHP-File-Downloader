<?php
defined("_CORE") or die("Access Denied!");

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
	return isset($_SERVER['HTTP_REFERER'])?strtolower($_SERVER['HTTP_REFERER']):'';
}

function get_ip(){
	return isset($_SERVER['HTTP_X_FORWARDED_FOR'])?$_SERVER['HTTP_X_FORWARDED_FOR']:$_SERVER['REMOTE_ADDR'];
}

function check_referrer($refs){
	if(!isset($_SERVER['HTTP_REFERER'])) return false;
	foreach($refs as $val){
		if(preg_match("#".trim($val)."#i", $_SERVER['HTTP_REFERER'])) return 1;
	}
	return false;
}

function log_download_info($log_file, $arr){
	if($handler = @fopen($log_file, 'a+')){
		@fputs($handler, implode("  ", $arr)."\n");
		@fclose($handler);
	}
}
?>