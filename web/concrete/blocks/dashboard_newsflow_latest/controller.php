<?php
namespace Concrete\Block\DashboardNewsflowLatest;
use Concrete\Controller\SinglePage\Dashboard\News;
use Loader;
use \Concrete\Core\Block\BlockController;
use \Concrete\Core\Activity\Newsflow;

/**
 * @property mixed $slot
 * Class Controller
 * @package Concrete\Block\DashboardNewsflowLatest
 */
class Controller extends BlockController {

	protected $btCacheBlockRecord = true;
	protected $btCacheBlockOutput = true;
	protected $btCacheBlockOutputOnPost = true;
	protected $btCacheBlockOutputLifetime = 7200;
	protected $btTable = 'btDashboardNewsflowLatest';
	protected $btCacheBlockOutputForRegisteredUsers = true;
	protected $btIsInternal = true;
	
	public function getBlockTypeDescription() {
		return t("Grabs the latest newsflow data from concrete5.org.");
	}
	
	public function getBlockTypeName() {
		return t("Dashboard Newsflow Latest");
	}

    public function view() {
        $newsflow = new Newsflow();
		// get the latest data as well
		$slots = $newsflow->getSlotContents();
		$this->set('slot', $slots[$this->slot]);
		
		// this is kind of a hack
		if ($this->slot == 'C') { 
			$ni = false;
			try
			{
				// in case we are not connected $ni will throw an exception ...
				$ni = $newsflow->getEditionByPath('/newsflow');
			}
			catch ( \Exception $e ) {}
			if ($ni !== false) {
				$this->set('editionTitle', $ni->getTitle());
				$this->set('editionDescription', $ni->getDescription());
				$this->set('editionDate', $ni->getDate());
				$this->set('editionID', $ni->getID());
			} else {
			
			}
		}
	}
	
}
