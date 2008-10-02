<?

class DiscussionPostModel {
	
	private $body = false;
	
	public function getSubject() {
		return $this->cvName;
	}
	
	public function getBody() {
		if ($this->body == false) {
			$a = new Area('Main');
			$b = $a->getAreaBlocksArray($this);
			$b1 = $b[0];
			$bi = $b1->getInstance();
			$this->content = $bi->content;
		}		
		return $this->body;
	}
	
}