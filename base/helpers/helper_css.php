<?php
/**
* Helper file
* CSS functions
* 
* @author AntonioCS
* @version 1.0
*/


class helper_css {

	public static function compress($buffer, $create_cache = true) {
		/* remove comments */
		$buffer = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $buffer);
		/* remove tabs, spaces, newlines, etc. */
		$buffer = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $buffer);
		return $buffer;
	}    
	
	public static function remove_csscache($file) {
	
	
	
	}
	
	public static function create_css_compressed_cache($file) {
	
	
	}
	/*
	$cssfiles = scandir($cssweb_dir);
	//remove . and ..
	array_shift($cssfiles);
	array_shift($cssfiles);
	
	$allcssfiles_compressed = '';
	
	clearstatcache(); //to clear the cache of the function is_file
	
	foreach ($cssfiles as $cssfile) {
		$f = $cssweb_dir . $cssfile;
		if (is_file($f))
			$allcssfiles_compressed .= compress(file_get_contents($f));    
	}
	
	file_put_contents($csscachedfile,$allcssfiles_compressed);
	
	echo $allcssfiles_compressed;
	*/

}
?>
