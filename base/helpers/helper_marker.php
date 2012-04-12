<?php 

class helper_marker {
	
	private static $markers = array();	
	

	public static function mark($name) {	
		self::$markers[$name] = microtime(TRUE);
	}
	
	public static function marktime($name,$format = false) {
		if (isset(self::$markers[$name])) {
			$t = (microtime(TRUE) - self::$markers[$name]);
			unset(self::$markers[$name]);
			if ($format)
				$t = date('Y-m-d G:i:s',$t);
			return $t;
		}
		return null;
	}	
}

?>