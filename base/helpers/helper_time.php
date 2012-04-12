<?php

/**
* Helper file
* 
* @author AntonioCS
* @version 1.0
* 
*/
class helper_time {
	
	
	public static $seconds = array(
			'year' => 31536000, //60 * 60 * 24 * 365
			'month' => 2592000, //60 * 60 * 24 * 30
			'week' => 604800,   //60 * 60 * 24 * 7
			'day' => 86400,     //60 * 60 * 24
			'hour' => 3600,     //60 * 60
			'minute' => 60
	);
	
	/**
	 * Calculate time to live
	 * 	
	 * @param string $ttl - A number given attached with s,m,h,d,w
	 * s = seconds
	 * m = minutes 
	 * h = hours
	 * d = days
	 * w = weeks
	 * 
	 */
	public static function calculatettl($ttl) {
		if ($ttl == 0)
			return 0;
			
		if (is_numeric($ttl)) //there is no marker, default to seconds and just return the number
			return $ttl;
		

		$marker = substr($ttl, -1); //get the letter (which should be the last item)
		$time = substr($ttl, 0,-1); //get the time					
		
		switch ($marker) {
			case 's': //do nothing
			break;			
			case 'm':
				$time *= self::$seconds['minute'];
			break;			
			case 'h':
				$time *= self::$seconds['hour'];
			break;			
			case 'd':
				$time *= self::$seconds['day'];
			break;			
			case 'w':
				$time *= self::$seconds['week'];
			break;
		}				
			
		return $time;
	}

	/**
	* Return Seconds from minutes given
	* 
	* @param mixed $minute
	*/
	public static function giveSeconds($minute) {
		return ($minute * 60);
	}
	
	/**
	* Return Minutes from hours given
	* 
	* @param mixed $hours
	*/
	public static function giveMinutes($hours) {
		return ($hours * 60);
	}
	
	

	/**
	* From http://stackoverflow.com/questions/18685/how-to-display-12-minutes-ago-etc-in-a-php-webpage
	* This will give the correct representation of a time value
	* 		
	* @param mixed $since
	*/
	public static function time_since($since) {		
		$chunks = array(      
			array(31536000,'year'), //60 * 60 * 24 * 365
			array(2592000,'month'), //60 * 60 * 24 * 30
			array(604800,'week'),   //60 * 60 * 24 * 7
			array(86400,'day'),     //60 * 60 * 24
			array(3600,'hour'),     //60 * 60
			array(60,'minute'),
			array(1,'second')
		);
		$count = $name = null;
		
		for ($i = 0, $j = count($chunks); $i < $j; $i++) {
			list($seconds,$name) = $chunks[$i];			
			if (($count = floor($since / $seconds)) != 0) //if we divide by a value that is larger it will always give 0 (0 point something which is then rounded off to 0 by using floor)
				break;
		}

		return (($count == 1) ? '1 '.$name : "$count {$name}s");		
	}
    
    /**
    * Return time time passed
    *                                                                                  
    * @param int $day 
    * @param int $month 
    * @param int $year
    * @param int $hours
    * @param int $minutes
    * @param int $seconds
    * @param array $formats - Custom formats
    *                         default structure:
    *                      array(
                            'sec' => 's\s',
                            'min' => 'i\m s\s',
                            'hours' => 'G\h i\m'     
                            )
    *           
    * @return string
    */
    public static function TimeAgo($day,$month,$year,$hours,$minutes,$seconds = 0, $custom_formats = array()) {
    
        $formats = array(
            'sec' => 's\s',
            'min' => 'i\m s\s',
            'hours' => 'G\h i\m',
            'day' => ''        
        );
        
        //Merge the formats.
        $formats = array_merge($formats,$custom_formats);

        //date_default_timezone_set('Europe/Lisbon');
    
        $result = time() - mktime($hours,$minutes,$seconds,$month,$day,$year);
        $format = null;

        switch (true) {
            case $result < 60:
                $format = $formats['sec'];
            break;
            case $result < 3600: //seconds in a hour
                $format = $formats['min'];
            break;
            case $result < 86400: //seconds in a day
                $format = $formats['hours'];
            break;
            default:          
        }

        if ($format)          
            return date($format,$result);
       
        //return $date;
    }
}