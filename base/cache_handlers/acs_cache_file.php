<?php


class acs_cache_file extends acs_cache_base {
	
	/**
	 * Options class
	 * @var stdClass
	 */
	private $_oclass = null;
	
	private $_useoptions = true;
	private $_optk = 'acs_cache_file_options_file';

    /**
    * Return cache directory 
    * 
    * @param string $subdir
    */
	private function cachedir($subdir = null) {
		$dir = $this->_settings['dir'];
		
		if ($subdir) {
			$subdir = helper_file::end_slash(helper_file::nix_slashes($subdir));			
			$dir .= $subdir;
		
			if (!is_dir($dir)) //If there is no folder create the folder with write permissions for owner and group
				mkdir($dir,0775,true);
		}
		
		if (!is_writable($dir))
			throw new acs_exception('Cache directory is unwritable: ' . $dir);
		
		return $dir;
	}
	
	/**
	 * Return the correct key and the path
	 * 
	 * @param string $key
	 * @param string $subdir
	 */
	private function keyPath($key,$subdir) {
		$key = $this->keyname($key . $subdir);
		return array($key ,$this->cachedir($subdir) . $key);
	}
		
	private function getOptions($key = null) {
		$k = $this->_optk;
		
		$this->_useoptions = false;
		$this->_oclass = $this->get($k);
		
		if ($this->_oclass === false) { //there is no cache file options!!
			$this->_oclass = new stdClass();
			$this->_oclass->tags = array();			
		}
		else 
			$this->_oclass = unserialize($this->_oclass);
		
		if ($key) {
			if (property_exists($this->_oclass, $key))
				return $this->_oclass->$key;
				
			return null;
		}	
	}
	
    /**
     * Save data in the cache with key
     * 
     * @param string $key
     * @param mixed $value
     * @param string $subdir
     * @param mixed $tags Marker to allow to delete cache types
     * @param string $ttl time to live
     * 
     * 
     */
	public function set($key,$value,$subdir = null,$tags = null,$ttl = null) {
		$created = time();
		$keyvalue = $key; //before moddfing the key
		list($key,$path) = $this->keyPath($key, $subdir);
		$serialized = false;
		
		if (is_array($value) || is_object($value)) {
			$value = serialize($value);
			$serialized = true;
		}
		
		file_put_contents($path, $value);
		
		if ($this->_useoptions) {
			
			//calculate ttl
			if ($ttl == null) //use default ttl
				$ttl = $this->_settings['ttl']; 
			
			if ($ttl > 0) //in case 0 was used to mark the cache unlimeted
				$ttl = $created + helper_time::calculatettl($ttl);
			if (!$this->_oclass)
				$this->getOptions();
	
			if ($tags) {
				if (!is_array($tags)) //to support multiple tags
					$tags = array($tags);
				
				foreach ($tags as $tag) {
					if (!isset($this->_oclass->tags[$tag]))
						$this->_oclass->tags[$tag] = array();
					
					$this->_oclass->tags[$tag][$key] = 1; 	
				}
			}		
			
			$this->_oclass->$key = (object) array(
					'serialized' => $serialized,
					'created' => $created,
					'ttl' => $ttl,
					'subdir' => $subdir,
					'keyvalue' => $keyvalue,
					'tags' => $tags
				);
		}
		
		$this->_useoptions = true;
	}
	
	/**
	 * 
	 * Delete element from options and from file system
	 * 
	 * @param string $key
	 * @param string $subdir
	 */
	public function delete($key, $subdir = null) {
		list($key,$path) = $this->keyPath($key, $subdir);

		if (file_exists($path) && unlink($path)) {
			if (!$this->_oclass)
				$this->getOptions();
			
			if ($this->_oclass->$key->tags) { //clear the values in the tags
				foreach ($this->_oclass->$key->tags as $tag) {
					if (isset($this->_oclass->tags[$tag][$key])) {
						unset($this->_oclass->tags[$tag][$key]);
						
						//Don't keep the tag if there are no values in it
						if (empty($this->_oclass->tags[$tag]))
							unset($this->_oclass->tags[$tag]);
					}
				}				
			}
			
			unset($this->_oclass->$key);			
			return true;
		}
		
		return false;
	}
	
	public function deleteTag($tag) {
		if (!$this->_oclass)
			$this->getOptions();
			
		if (isset($this->_oclass->tags[$tag])) {
			foreach ($this->_oclass->tags[$tag] as $key => $value) { //value is just the 1 it doesn't matter
				$k = $this->_oclass->$key->keyvalue;
				$subdir = $this->_oclass->$key->subdir;
				
				$this->delete($k, $subdir);
			}
		}
		
	}
	
	/**
	 * Save the cache options file
	 */
	private function setOptions() {
		$this->_useoptions = false;
		$this->set($this->_optk, $this->_oclass);		
	}
	
	/**
	 * (non-PHPdoc)
	 * @see acs_cache_base::get()
	 */
	public function get($key, $subdir = null) {
		//$key = $this->keyname($key . $subdir);
		//$path = $this->cachedir($subdir) . $key;
		$realkey = $key; //must save the original key because I might need to send it to the delete method (which will process the key)
		list($key,$path) = $this->keyPath($key, $subdir);
		
		$data = false;

		if (file_exists($path)) {
			$data = file_get_contents($path);

			if ($this->_useoptions) {
				$o = $this->getOptions($key);
				
				if ($o) {												
					if ($o->ttl && $o->ttl < time()) {//it does not have unlimeted time and it has surpassed it's time to live
						$data = false;
						$this->delete($realkey, $subdir);
					}
	
					if ($data && $o->serialized) //make sure the data has not been destroied
						$data = unserialize($data);
				}	
			}	
			$this->_useoptions = true;	
		}
		
		return $data;
	}
	
	public function __destruct() {
		if ($this->_oclass)
			$this->setOptions();
	}
}
