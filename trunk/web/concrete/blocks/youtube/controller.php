<?
	class YoutubeBlockController extends BlockController {
		
		var $pobj;
		
		protected $btDescription = "Embeds a Youtube Video in your web page.";
		protected $btName = "Youtube Video";
		protected $btTable = 'btYouTube';
		protected $btInterfaceWidth = "400";
		protected $btInterfaceHeight = "170";
		
		public $title = "My Video";
		public $videoURL = "";
		public $mode = "youtube";
		
		function view(){ 
			$this->set('bID', $this->bID);	
			$this->set('title', $this->title);
			$this->set('videoURL', $this->videoURL);
			$this->set('mode', $this->mode);
		}
		
		function save($data) { 
			$args['title'] = isset($data['title']) ? trim($data['title']) : '';
			$args['videoURL'] = isset($data['videoURL']) ? trim($data['videoURL']) : '';			
			parent::save($args);
		}
		
	}
	
?>