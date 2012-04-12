<?php

/**
* Validate data class 
*/
class acs_validator {

	/**
	 * Value to be analysed
	 * 
	 * @var mixed
	 */
    public $value = null;
	
	/**
	 * Matches in regex pattern look ups
	 * @var array
	 */
    public $match = array();
	
	/**
	 *
	 * @var int
	 */
    public $match_total = 0;
    
    public $date_format = 'Y-m-d';
    public $time_format = 'H:i:s';
    public $date_time_format = 'Y-m-d H:i:s';

    public function __construct($value = null) {
        $this->value = $value;    
    }

    //Int range –2147483648 to 2147483647 
	/**
	 * Is value int
	 * 
	 * @param mixed $value
	 * @return bool 
	 */
    public function isInt($value = null) {
        $this->value = filter_var(($value ? $value : $this->value), FILTER_SANITIZE_NUMBER_INT); 
        return filter_var($this->value,FILTER_VALIDATE_INT);
    }
    
	/**
	 *
	 * @param type $x
	 * @param type $y
	 * @param type $value
	 * @return type 
	 */
    public function isIntBetween($x,$y, $value = null) {
        $this->value = ($value ? $value : $this->value);
        if ($this->isInt($this->value)) {
            $options['options'] = array('min_range' => $x,'max_range' => $y);
            return filter_var($this->value,FILTER_VALIDATE_INT,$options);      
        }    
        return false;
    }

    //http://www.php.net/manual/en/function.is-float.php#79002
    public function isFloat($value = null) {
        $this->value = ($value ? $value : $this->value); 
        return ((is_float($this->value) || ((float)$this->value > (int)$this->value || strlen($this->value) != strlen((int)$this->value)) && (int)$this->value != 0));
    }

    public function isNumeric($value = null) {
        return (is_numeric(($value ? $value : $this->value)));
    }  
    
    public function isNumericBetween($x,$y,$value = null) {
        $this->value = ($value ? $value : $this->value);
        if ($this->isNumeric($value)) 
            return ($this->value >= $x && $this->value <= $y);    
        return false;    
    }
    
    public function isNotZero($value = null) {
        $this->value = ($value ? $value : $this->value);
        return ($this->isNumeric($this->value) && $this->value > 0);    
    }

    public function isAlphaNumeric($value = null) {
    	$this->value = ($value ? $value : $this->value);
        return (ctype_alnum($this->value));    
    }
    
    public function isAlpha($value = null) {
    	$this->value = ($value ? $value : $this->value);
        return (ctype_alpha($this->value));
    }
    
    public function isNotNull($value = null) {
        $this->value = ($value ? $value : $this->value);
        return ($this->value !== null && $this->value != '');
    }
    
    public function isNull($value = null) {
        $this->value = ($value ? $value : $this->value);
        return ($this->value === null || $this->value == '');
    }

    public function isEmail($value = null) {        
        $this->value = filter_var(($value ? $value : $this->value), FILTER_SANITIZE_EMAIL);
        return (bool)filter_var($this->value, FILTER_VALIDATE_EMAIL);    
    }

    public function isMatchRegex($pattern, $value = null) {
        $this->value = ($value ? $value : $this->value);
        return preg_match($pattern,$this->value,$this->match);
    }
    
    public function isMatchRegexAll($pattern, $value = null) {
        $this->value = ($value ? $value : $this->value);
        $this->match_total = preg_match_all($pattern,$this->value,$this->match);
        return (bool)$this->match_total;
    }

    public function isMatch($pattern, $value = null) {
        $this->value = ($value ? $value : $this->value);       
        return ($this->value == $pattern);
    }
    
    public function isEq($data, $value = null) {
    	$this->value = ($value ? $value : $this->value);
    	return ($this->value == $data);
	}
	
	public function isNotEq($data, $value = null) {
    	$this->value = ($value ? $value : $this->value);
    	return ($this->value != $data);
	}
	
	public function isLength($len,$value = null) {
		$this->value = ($value ? $value : $this->value);
    	return (strlen($this->value) >= $len); 
	}
	 
	public function isLengthSmaller($len,$value = null) {
		$this->value = ($value ? $value : $this->value);
    	return (strlen($this->value) <= $len); 
	} 
    
    public function isArray() { 
        return (is_array($this->value) && !empty($this->value));
    }

    public function isIP($value = null) {
        $this->value = ($value ? $value : $this->value);
        return filter_var($this->value,FILTER_VALIDATE_IP);
    }
    
    public function isIP_v4($value = null) {
        $this->value = ($value ? $value : $this->value);
        return filter_var($this->value,FILTER_VALIDATE_IP,FILTER_FLAG_IPV4);
    }
    
    public function isIP_v6($value = null) {
        $this->value = ($value ? $value : $this->value);
        return filter_var($this->value,FILTER_VALIDATE_IP,FILTER_FLAG_IPV6);
    }

    public function isURL($value = null) {
        $this->value = ($value ? $value : $this->value);
        return filter_var($this->value,FILTER_VALIDATE_URL);
    }
    
    public function isDate($value = null) {
        $this->value = ($value ? $value : $this->value);
        $date = strtotime($this->value); 
        if ($date) { //Must check if it's not null because if it is date() will just use time()
          list($year,$month,$day) = explode('-',date($this->date_format,$date));
          return checkdate($month,$day,$year);    
        }
        return false;
    }
    
    public function isDateTodayOrBeyond($value = null) {
        $this->value = ($value ? $value : $this->value);
        $date = strtotime($this->value);
        if ($date) {
          list($year,$month,$day) = explode('-',date($this->date_format,$date));
          list($currentYear,$currentMonth,$currentDay) = explode('-',date('Y-m-d'));        
          if (checkdate($month,$day,$year)) {
              if ($year > $currentYear)
                  return true;            
              elseif ($year == $currentYear) {
                  if ($month > $currentMonth)
                      return true;
                  elseif ($month == $currentMonth) 
                      return ($day >= $currentDay);                        
              }    
          }
        }
        return false;
    }
    
    public function isTime($value = null) {        
        $this->value = ($value ? $value : $this->value);
        $time = strtotime($this->value);
        if ($time) {
          list($hour,$minute,$second) = explode(':',date($this->time_format,$time));
          return (($hour >= 0 && $hour <= 23) && ($minute >= 0 && $minute <= 59) && ($second >= 0 && $second <= 59));
        }
        return false;
    }
    
    public function __toString() {
    	return $this->value;
    }
}