<?

class Cache extends CacheTemplate {

	protected static $filePrefix='ccm_cache_';
	
	/** 
	 * Inserts or updates an item to the cache
	 */	
	public function set($type, $id, $obj, $expire = 0){
		if (ENABLE_CACHE == false) {
			return false;
		}
		if(intval($expire)==0) return false;
		
		//create an object to store the data and the expiration
		$key = parent::key($type, $id); 
		$cacheDataObject = new Object();
		$cacheDataObject->expires = date('U')+intval($expire);
		$cacheDataObject->data = $obj;
		
		$fileHandle = fopen( self::getFilePath($key), "w+");
		fputs($fileHandle, serialize($cacheDataObject) );
		fclose($fileHandle);
		
		$loc = CacheLocal::get();
		$loc->cache[$key] = $obj;		
	}
	
	/** 
	 * Retrieves an item from the cache
	 */	
	public function get($type, $id){
		if (ENABLE_CACHE == false) {
			return false;
		}		
		$key = parent::key($type, $id);
		
		//check the local (in memory) cache first
		$loc = CacheLocal::get();
		if (isset($loc->cache[$key])){
			$value = $loc->cache[$key];
			
		//check the file system for a cached version	
		}else{		
			$filePath=self::getFilePath($key);			
			if(!file_exists($filePath)) return false;
			
			//load the cached object
			$cacheDataObject=unserialize(file_get_contents($filePath));		
			
			//has the data expired? if so, remove it 
			if( $cacheDataObject->expires<date('U') ){
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
		if (ENABLE_CACHE == false) {
			return false;
		}		
		
		$key = parent::key($type, $id);
		$filePath=self::getFilePath($key);
		if (!strstr($filePath,'.') && strstr($filePath,self::$filePrefix) && is_file($filePath)){
			unlink($filePath);
			$loc = CacheLocal::get();
			unset($loc->cache[$key]);
		}				
	}	
	
	/** 
	 * Completely flushes the cache
	 */	
	public function flush(){
		if ($handle = opendir(DIR_FILES_CACHE) ) {
			while (false !== ($file = readdir($handle))){
				$filePath=DIR_FILES_CACHE.'/'.$file; 
				if (!strstr($filePath,'.') && strstr($filePath,self::$filePrefix) && is_file($filePath))
					unlink($filePath); 
			}
			closedir($handle);		
		}
	}	
	
	protected function getFilePath($key=''){
		return DIR_FILES_CACHE.'/'.self::$filePrefix.$key.'';
	}
}


?>