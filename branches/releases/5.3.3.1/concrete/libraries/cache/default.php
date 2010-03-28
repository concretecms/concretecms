<?php 

// This is ugly. Fix later.
$GLOBALS['ccmRuntimeCacheEnabled'] = true;
$GLOBALS['ccmRuntimeLocalCacheEnabled'] = true;

class Cache extends CacheTemplate { 

	protected static $filePrefix='object_';
	
	public function startup() {
		if (!is_dir(DIR_FILES_CACHE_CORE) || !is_writable(DIR_FILES_CACHE_CORE)) {
			if (!@mkdir(DIR_FILES_CACHE_CORE)) {
				define("ENABLE_CACHE", false);
			} else {
				@chmod(DIR_FILES_CACHE_CORE, 0777);
			}
		}	
	}
	
	/** 
	 * Inserts or updates an item to the cache
	 * If $forceSet is true, we sidestep ENABLE_CACHE. This is for certain operations that
	 * the cache must always be enabled for (getting remote data, etc..)
	 */	
	public function set($type, $id, $obj, $expire = 0, $forceSet = false){
		if (!$GLOBALS['ccmRuntimeCacheEnabled']) {
			return false;
		}
		if ((!defined('ENABLE_CACHE')) || (ENABLE_CACHE == false && $forceSet == false)) {
			return false;
		}
		
		//if(intval($expire)==0) return false;
		
		//create an object to store the data and the expiration
		$key = parent::key($type, $id); 
		$cacheDataObject = new Object();
		if(!$expire) $cacheDataObject->expires = 0;
		else $cacheDataObject->expires = date('U')+intval($expire);
		$cacheDataObject->timestamp = time();
		$cacheDataObject->data = $obj;
		
		$fileHandle = fopen( self::getFilePath($key), "w+");
		fputs($fileHandle, serialize($cacheDataObject) );
		fclose($fileHandle);
		
		if ($GLOBALS['ccmRuntimeLocalCacheEnabled']) {
			$loc = CacheLocal::get();
			$loc->cache[$key] = $obj;
		}
	}
	
	/** 
	 * Retrieves an item from the cache
	 * If $forceGet is true, we sidestep ENABLE_CACHE. This is for certain operations that
	 * the cache must always be enabled for (getting remote data, etc..)
	 */	
	public function get($type, $id, $mustBeNewerThan = false, $forceGet = false){
		if (!$GLOBALS['ccmRuntimeCacheEnabled']) {
			return false;
		}
		if ((!defined('ENABLE_CACHE')) || (ENABLE_CACHE == false && $forceGet == false)) {
			return false;
		}

		$key = parent::key($type, $id);
		
		//check the local (in memory) cache first
		$loc = CacheLocal::get();
		if (($GLOBALS['ccmRuntimeLocalCacheEnabled']) && isset($loc->cache[$key])) {
			$value = $loc->cache[$key];
			
		//check the file system for a cached version	
		}else{		
			$filePath=self::getFilePath($key);		
			if(!file_exists($filePath)) return false;
			
			//load the cached object
			$cacheDataObject=unserialize(file_get_contents($filePath));		
			
			// If $mustBeNewerThan set? Is the timestamp of the cache item older than $mustBeNewerThan? If so, we remove.
			// This is useful when you're dealing with caching the contents of files, and the files are changing on the
			// drive, and you pass the filemtime to this function to ensure you always cache an updated version
			if ($mustBeNewerThan != false && is_object($cacheDataObject) && $cacheDataObject->timestamp < $mustBeNewerThan) {
				self::delete($type, $id);
				return false;
			}
		
			//has the data expired? if so, remove it 
			if( $cacheDataObject->expires<date('U') && $cacheDataObject->expires!=0 ){
				self::delete($type, $id);
				return false;
			}
			$value = $cacheDataObject->data;
		}
		
		if ( $value===NULL ) return false;
		
		return $value;
	}
	
	/** 
	 * Removes an item from the cache
	 */	
	public function delete($type, $id){
		if (!$GLOBALS['ccmRuntimeCacheEnabled']) {
			return false;
		}
		if ((!defined('ENABLE_CACHE')) || (ENABLE_CACHE == false)) {
			return false;
		}		
		
		$key = parent::key($type, $id);
		$filePath=self::getFilePath($key);
		if (strstr($filePath,self::$filePrefix) && is_file($filePath)){
			unlink($filePath);
			if ($GLOBALS['ccmRuntimeLocalCacheEnabled']) {
				$loc = CacheLocal::get();
				unset($loc->cache[$key]);
			}
		}				
	}
	
	/** 
	 * Completely flushes the cache
	 */	
	public function flush(){
		if (!$GLOBALS['ccmRuntimeCacheEnabled']) {
			return false;
		}
		if ($handle = opendir(DIR_FILES_CACHE_CORE) ) {		
			while (false !== ($file = readdir($handle))){
				$filePath=DIR_FILES_CACHE_CORE.'/'.$file; 
				if (!strstr($file,'.') && strstr($filePath,self::$filePrefix) && is_file($filePath)){ 
					unlink($filePath); 
				}
			}
			closedir($handle);		
		} 
		return true;
		
		// another flush method
		/*
		rename(DIR_FILES_CACHE_CORE, DIR_FILES_CACHE_CORE . time());
		mkdir(DIR_FILES_CACHE_CORE);
		*/
	}
	
	protected function getFilePath($key=''){
		return DIR_FILES_CACHE_CORE.'/'.self::$filePrefix.$key.'';
	}
	
	public function disableCache() {
		$GLOBALS['ccmRuntimeCacheEnabled'] = false;
	}
	
	public function disableLocalCache() {
		$GLOBALS['ccmRuntimeLocalCacheEnabled'] = false;
	}
	
	public function enableCache() {
		$GLOBALS['ccmRuntimeCacheEnabled'] = true;
	}

	public function enableLocalCache() {
		$GLOBALS['ccmRuntimeLocalCacheEnabled'] = true;
	}
	
}


?>