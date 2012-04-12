<?php 


//make this class a singleton for the other classes!!!
class acs_cache  {
	
	/**
	 *
	 * @var type 
	 */
	private static $_instances = array();
	
	/**
	 *
	 * @var string
	 */
	private static $_cachehandlersdir = 'cache_handlers/';
	
	
	/**
	 * Return cache type
	 * 
	 * @param string $cachetype
	 * @throws acs_exception
	 * @return acs_cache_base
	 */
	public static function getCache($cachetype = null) {
		
		//Use default cache type if nothing given
		if (!$cachetype)		
			$cachetype = acs_config::getInstance()->cache['cache_type'];
		
		
		if (!isset(self::$_instances[$cachetype])) {
			$config = acs_config::getInstance();
			$settings = null;
			
			$path = $config->base_dir . self::$_cachehandlersdir; 
			$cachebase = $path . 'acs_cache_base.' . $config->common_extension ;
			
			$cacheclass = 'acs_cache_' . $cachetype;
			$cachefile = $path . $cacheclass . '.' . $config->common_extension;
			
			if (isset($config->cache['settings'][$cachetype]))
				$settings = $config->cache['settings'][$cachetype];
			
			if (file_exists($cachefile)) {
				require_once($cachebase);
				require($cachefile);
				
				self::$_instances[$cachetype] = new $cacheclass($settings);
			}
			else 
				throw new acs_exception('Cache type not found');
		}
		
		return self::$_instances[$cachetype]; 
	}
}