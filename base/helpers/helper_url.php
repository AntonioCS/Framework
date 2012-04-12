<?php

class helper_url {
	
	/**
	* Refresh the current page
	* 
	*/
	public static function refresh() {
		self::redirect(helper_url::getURL());
	}

	/**
	 * Returns the nth argument of the query string
	 *
	 * @param integer $n
	 * @param bool $fromthatpointon - set to true if you want to return all parameters from a certain point forward
	 * 
	 * @return mixed the specified item, null or if  $fromthatpointon is true I return an array starting at $n 
	 *                      and ending when the number items stops
	 */
	public static function getserverarg($n = 1,$fromthatpointon = false) {        
		if (self::getserverargcount() > 0) {
			$sqs = self::getargs();   			
				   
			if ($n === 0)
				return $sqs; 
				
			$n--;//The array starts at 0 so we decrease to make it right			
			$items = explode('/',$sqs);
			if (isset($items[$n])) {
				if ($fromthatpointon) {
					$frthpo_items[] = $items[$n];
					for ($i = $n+1, $titems = count($items);$i < $titems;$i++) //let's add all the items starting at n+1
						$frthpo_items[] = $items[$i]; 	
					
					return $frthpo_items; 
				}			
				return $items[$n];  
			}          
		}        
		return null;				
	}			
	
	/**
	 * Breacks the query string into an array using the / as a separator
	 *
	 * @return the query string as an array
	 */
	public static function getserverargcount() {		
		return count(explode('/',self::getargs()));
	}
	
	/**
	* This will return all the argurments of the query string if the router hasn't set a custom route
	* 
	*/	
	private static function getargs() {
		//return ((acs_router::$customRoute) ? acs_router::$customRoute : $_SERVER['QUERY_STRING']);   
        return $_SERVER['QUERY_STRING'];
	}
	
	/**
	 * Return a <a> tag with the url and name in it
	 *
	 * @deprecated Don't use this. It's an offense to all that is good about the mvc pattern
	 * 
	 * @param string $link_url
	 * @param string $link_name
	 * @param array $options null
	 * @return proper link tag
	 */
	public static function link($link_url,$link_name,$options = null) {		
		$l = '<a href="'. self::correct_url($link_url) .'" title="' . $link_name .'" {OPTIONS}>' . $link_name . '</a>';		
		$options_ = '';
		if (is_array($options)) {			
			foreach ($options as $tagname => $tagvalue) 
				$options_ .= $tagname . '="' . $tagvalue . '" ';
		}	
		$l = str_replace('{OPTIONS}',$options_,$l);                       		
		
		return $l;		
	}
	
	/**
	* Will return true if the string given starts with http:// or https://
	* 
	* @param string $string The url
	* @return bool
	*/
	public static function ishttp($string) {
		return (strncasecmp($string,'http://',7) == 0 || strncasecmp($string,'https://',8) == 0);
	}
	
	/**
	 * @desc Corrects the url. If I am using modrewrite this will return the value of the uri with the url, if not it will add a ? in the middle
	 *
	 * @param string $url
	 * @return url value corrected
	 */
	public static function correct_url($url) {
		//Let's check if I am not redirecting to somewhere outside the framework
		//if (strncasecmp($url,'http://',7) == 0 || strncasecmp($url,'https://',8) == 0)
		if (self::ishttp($url))
			return $url;		
				
		$configData = acs_config::getInstance();        		
		return $configData->uri . ((!$configData->using_modrewrite) ? '?' : '') . $url;
	}
	
	/**
	 * 
	 *
	 * @return unknown
	 */
	public static function getURL() {
		return self::correct_url($_SERVER['QUERY_STRING']);
	}
	
	/**
	 * Shortcut to the header("Location: ") function
	 *
	 * @param string $location URL to goto
	 * @param array $options Options for the redirect - not implemented
	 * 	Values of the array can be:
	 * 		Delay - Time to wait (in seconds)
	 * 		Meta - Bool value. If this is set to true the redirect method will use the meta-refresh instead of redirecting with js (only checked if there is a delay)
	 *      Msg - String. String to desplay
	 */
	public static function redirect($location) { //,array $options) {	
		session_write_close(); //As recommend in http://pt.php.net/session_write_close (in the comments)
		header('Location:' . self::correct_url($location));
		exit();
	}
	
	/**
	 * @desc Show a 400-500 Header error within the site theme (taken from MicroMVC with a few changes)
	 *
	 * @access	public
	 * @param	string
	 * @return	void
	 */
	public static function request_error($type='404') {

		//Clean the type of error from XSS stuff
		$type = preg_replace('/[^a-z0-9]+/i', '', $type);
		$header = null;
			
		switch ($type) {
			case 400:
				$header = 'HTTP/1.0 400 Bad Request';
			break;
			case 401:
				$header = 'HTTP/1.0 401 Unauthorized';
			break;
			case 403:
				$header = 'HTTP/1.0 403 Forbidden';
			break;
			case 500:
				$header = 'HTTP/1.0 500 Internal Server Error';
			break;
			default:
				$header = 'HTTP/1.0 404 Not Found';
		}
		header($header);
	}
    
    
    /**
    * Return a refer only if it's a valid one
    * 
    */
    public static function referer() {
        if (isset($_SERVER['HTTP_REFERER'])) {
            $r = $_SERVER['HTTP_REFERER'];
            $l = acs_config::getInstance()->uri;
            
            if (substr($r,0,strlen($l)) == $l)
                return $r;
        }        
        return null;    
    }
}