<?php

class acs_cache_base {
	
	protected $_settings = null;

	public function __construct($settings) {
		$this->_settings = $settings;
	}
	
	protected function keyname($key, $mode = 0) {
		//Using sha1 because I must use one way of creating a key. If I have many ways I might not get the correct value
		return sha1($key);
		/*
		switch ($mode) {
			case 0:
				$key = helper_strings::replletras($key,true);
			break;
			case 1:
				$key = sha1($key);
			break;
			case 2:
				$key = md5($key);
			break;
		}
		
		return $key;
		*/
	}
	
	/**
	 * Save data in the cache with key
	 * 
	 * @param string $key
	 * @param mixed $value
	 * @param string $subdir
	 * @param mixed $tags Marker to allow to delete cache types
	 * @param mixed $ttl time to live
	 */
	public function set($key,$value,$subdir = null,$tags = null,$ttl = null) {	}

	/**
	 * Get item from cache
	 * 
	 * @param string $key
	 * @param string $subdir
	 */
	public function get($key, $subdir = null) {	}
}