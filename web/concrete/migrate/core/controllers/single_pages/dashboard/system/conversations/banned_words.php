<?php defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Controller_Page_Dashboard_System_Conversations_BannedWords extends DashboardPageController {

	public $bannedWords;

	public function view() {
		$this->set('bannedWords',$this->getBannedWords());
		$this->set('bannedListEnabled',Config::get('CONVERSATION_DISALLOW_BANNED_WORDS'));
	}

	public function getBannedWords() {
		if ($this->bannedWords) return $this->bannedWords;
		$bw = new BannedWordList();
		$this->bannedWords = $bw->get();
		return $this->bannedWords;
	}

	public function success() {
		$this->view();
		$this->set('message','Updated Banned Words.');
	}

	public function save() {
		$db = Loader::db();
		$db->execute("TRUNCATE TABLE BannedWords");
		$db->execute("ALTER TABLE BannedWords AUTO_INCREMENT=0");
		foreach ($this->post('banned_word') as $bw) {
			BannedWord::add($bw);
		}
		Config::save('CONVERSATION_DISALLOW_BANNED_WORDS',!!$this->post('banned_list_enabled'));
		$this->view();
		$this->redirect('dashboard/system/conversations/banned_words/success');
	}

}