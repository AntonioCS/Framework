<?php

class helper_misc {
	
	/**
	 * Create a seed for the mt_rand function (Taken from the php manual)
	 *
	 * @return seed
	 */
	public static function make_seed() {
  		list($usec, $sec) = explode(' ', microtime());
  		return (float) $sec + ((float) $usec * 100000);
	}
	
	
	/**
	 * Return a random number using the mt_rand function
	 *
	 * @param integer $min - minum number 
	 * @param integer $max - max number
	 * @return integer
	 */
	public static function getrandom($min = 1,$max = 1000000000) {
		mt_srand(self::make_seed());	
		if ($min && $max && $max > $min)
			return mt_rand($min,$max);	
		return mt_rand();
	}
    
    /**
     * Return a random number using the web site random.org
     *
     * @param integer $min - minum number
     * @param integer $max - max number 
     * @param integer $base
     * @return integer
     */
    public static function true_random($min = 1,$max = 1000000000,$base = 10) {
        //Error: The maximum value must be an integer in the [-1000000000,1000000000] interval 
        if ($max > 1000000000 || $min < -1000000000 || !in_array($base,array(16,10,8,2)))
            return null;        
            
        $ramdomorg = "http://www.random.org/integers/?num=1&min=$min&max=$max&col=1&base=$base&format=plain";

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $ramdomorg);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);

        $num = curl_exec($ch);

        curl_close($ch);
        
        return $num;    
    }
    
    /*
    * Unique id generator by Stefan Esser
    * 
    */
    public static function true_uniqueid() {
        return md5(
            microtime() . 
            uniqid( mt_rand(), true ) . 
            implode('', fstat(fopen( __FILE__, 'r' )))
        );	
    }
    
    
	public static function postoutput() {
		echo '<pre>';
		print_r($_POST);		
	}
		
	/**
	 * Function to escape regex characters
	 *
	 * @param string $text
	 * @return string with regex characteres escaped
	 */
	public static function regexEscape($text) {
		$regex_operators = array(
									'.', '*', '?', '+', '(', ')', '{', '}', '[', ']', '^','$','/'
								);
		$regex_escape_operators = array(
									'\.', '\*', '\?', '\+', '\(', '\)', '\{', '\}', '\[', '\]', '\^','\$','\/'
								);
		return str_replace( 	
								$regex_operators,
								$regex_escape_operators,
								$text
							);	
	}
    
    
    
    public static function htmlencase($text,$title = null) {
        return  "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">
                <html xmlns=\"http://www.w3.org/1999/xhtml\">
                <head>
                <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />
                <title>$title</title>
                </head>

                <body>
                $text
                </body>
                </html>";       
    }
    
    /**
    *  Function to check if the given date (just month and day) is in the range $starDate - $enDate
    *  The $datetoCheck, $startDate and $endDate must be in Month[separator]Day format
    * 
    * @param <partial_date> $datetoCheck
    * @param <partial_date> $startDate
    * @param <partial_date> $endDate
    * @param <char> $separator - Default = /
    *
    * //Test case    
    var_dump(indate('4/5','3/5','5/5')); //Return value: True
    var_dump(indate('2/5','3/5','5/5')); //Return value: False
    var_dump(indate('3/4','3/5','5/5')); //Return value: False
    var_dump(indate('3/5','3/5','5/5')); //Return value: True
    var_dump(indate('5/6','3/5','5/5')); //Return value: False      
    */
    public static function indate($datetoCheck,$startDate,$endDate, $separator = '/') {    
        list($checkmonth,$checkday) = explode($separator,$datetoCheck);
        list($startmonth,$startday) = explode($separator,$startDate);
        list($endmonth,$endday) = explode($separator,$endDate);

        if ($checkmonth < $startmonth || $checkmonth > $endmonth)
            return false;

        if ($checkmonth < $endmonth) {
            if ($checkday < $startday)
                return false;
        }
        else {
            if ($checkday > $endday)
                return false;
        }

        return true;
    }   	
}