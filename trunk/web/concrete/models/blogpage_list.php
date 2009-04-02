<?

defined('C5_EXECUTE') or die(_("Access Denied."));

Loader::model('page_list');
class BlogpageList extends PageList {
	const BLOCK_HANDLE_GUEST 	= 'guestbook'; 
	
	public function get($itemsToGet = 0, $offset = 0, $populateGuestbookInformation=true){
		$page_list = parent::get($itemsToGet, $offset);
		if($populateGuestbookInformation){
			foreach($page_list as $page){
				$page->guestbook_id = 0;
				$blocks = $page->getBlocks();
				foreach($blocks as $block){
					if ($block->btHandle == self::BLOCK_HANDLE_GUEST){
						$page->guestbook_id    = $block->bID;
						$ca = new Cache();
						$page->guestbook_count = $ca->get('GuestBookCount',$block->bID);
						$page->guestbook_count = $page->guestbook_count ? $page->guestbook_count : '0';
						
						break; //break out of foreach after you've found a guestbook
					}
				}
			}						
		}
		return $page_list;
	}
	public function populateGuestbookInformation(){

	}
}