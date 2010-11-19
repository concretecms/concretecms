<?php 

defined('C5_EXECUTE') or die("Access Denied.");
class NotesDashboardModuleController extends DashboardController {
	
	public function __construct() {
		$u = new User();
		$this->uc = Collection::getByHandle("uID=" . $u->getUserID());
		
		$myNotes = "";
		$bl = $this->uc->getBlocks('dashboard_notes');
		if (is_object($bl[0])) {
			$bo = $bl[0];
			$this->notesBlock = $bo;
			$bc = $bl[0]->getInstance();
			$myNotes = $bc->content;
		}
		$this->set('myNotes', $myNotes);
	}
	
	public function save() {	
		if (isset($this->notesBlock)) {
			$this->notesBlock->delete();
		}		
		$data['content'] = $this->post('dashboard_notes');
		$bt = BlockType::getByHandle('content');
		$this->uc->addBlock($bt, 'dashboard_notes', $data);
		$this->redirect('/dashboard/', 'module','notes','notes_saved'); 
	}
	
	public function notes_saved() {
		return t('Your dashboard notes were saved.');
	}
	
}