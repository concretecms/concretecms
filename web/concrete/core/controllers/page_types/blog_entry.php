<?
	defined('C5_EXECUTE') or die("Access Denied.");
	class Concrete5_Controller_PageType_BlogEntry extends Controller {
	
		/**
		 * Returns a formatted text for the number of comments in the first comment block in the "Entry Comments" area
		 * @param string $singular_format
		 * @param string $plural_format
		 * @param string $disabled_message
		 * @return string
		 */
		public function getCommentCountString($singular_format, $plural_format, $disabled_message = '') {
			$count = 0;
			$comments_enabled = false;
			
			$c = $this->getCollectionObject();
			$a = new Area('Blog Post Footer');
			$blocks = $a->getAreaBlocksArray($c);
			if(is_array($blocks) && count($blocks) > 0) {
				foreach($blocks as $b) {
					if($b->getBlockTypeHandle() == 'guestbook') {
						$controller = $b->getInstance();
						$count = $controller->getEntryCount($c->getCollectionID());
						$comments_enabled = true;
						break;// stop at the fist guestbook block found
					}	
				}
			}
			
			if($comments_enabled) {
				$format = ($count == 1 ? $singular_format : $plural_format);
				return sprintf($format, $count);
			} else {
				return $disabled_message;
			}
		}
		
	}