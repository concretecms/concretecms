<?

	abstract class CacheTemplate {
		
		abstract protected function key($type, $id);
		abstract public function delete($type, $id);
		abstract public function flush();
		abstract public function set($type, $id, $obj, $expire = 0);
		abstract public function get($type, $id);
		abstract public function stats();
	
	}