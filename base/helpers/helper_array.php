<?php
/**
* Helper file
* Array functions
* 
* @author AntonioCS
* @version 1.0
*/


class helper_array {
  /**
  * Function to remove duplicate entries in an array. Only works for one level arrays
  *
  * @param array $array (array ref)
  * @return void
  */
/*	-- Same thing as array_unique
public static function removeDuplicates(&$array) {
		$newarray = array();
	
		foreach ($array as $arrayvalue) 
			if (!in_array($arrayvalue,$newarray))
				$newarray[] = $arrayvalue;
		
		$array = $newarray;
	}
	*/
	
	/**
	* Method do rename a key in an array
	* 
	* @param mixed $newkey 
	* @param mixed $oldkey
	* @param array $array
	* @param bool $preserver_order - If true the order will be preserved, defaults to false
	*/
	public static function renamekey($newkey,$oldkey,array &$array, $preserver_order = false) {
		if (!isset($array[$oldkey]))
			return null;
						
		if ($preserver_order) {
			$newarray = array();
			foreach ($array as $k => $v) 
				$newarray[($k === $oldkey ? $newkey : $k)] = $v;			
			
			$array = $newarray;
		}
	
		else {
			$array[$newkey] = $array[$oldkey];
			unset($$array[$oldkey]);		
		}
	}
    
    /**
    * Inject value at the position and reset the array
    * 
    * @param array $array
    * @param int $position
    * @param mixed $value
    */
    public static function inject(&$array,$position,$value) {    

        if ($position == 0)
            $array = array_merge(array($value),$array);
        elseif (!isset($array[$position])) 
            $array[$position] = $value;
        else {
            $part1 = array_slice($array,0,$position);                
            $part2 = array_merge(array($value),array_slice($array,$position));

            $array = array_merge($part1,$part2);
        }
    }
}