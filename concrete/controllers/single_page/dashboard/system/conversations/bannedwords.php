<?php

namespace Concrete\Controller\SinglePage\Dashboard\System\Conversations;

use \Concrete\Core\Page\Controller\DashboardPageController;
use Config;
use Loader;
use \Concrete\Core\Validation\BannedWord\BannedWordList;
use \Concrete\Core\Validation\BannedWord\BannedWord;

class BannedWords extends DashboardPageController {

	public $bannedWords;

	public function view() {
		$this->set('bannedWords',$this->getBannedWords());
		$this->set('bannedListEnabled',Config::get('conversations.banned_words'));
	}

	public function getBannedWords() {
		if ($this->bannedWords) return $this->bannedWords;
		$bw = new BannedWordList();
		$this->bannedWords = $bw->get();
		return $this->bannedWords;
	}

	public function success() {
		$this->view();
		$this->set('message',t('Updated Banned Words.'));
	}

	public function save() {
		$this->view();

		/** @var Token $token */
		$token = \Core::make('token');

		if (!$token->validate("update_banned_words")) {
			$this->error->add('Invalid Token.');
			return;
		}

		$db = Loader::db();
		$db->execute("TRUNCATE TABLE BannedWords");
		$db->execute("ALTER TABLE BannedWords AUTO_INCREMENT=0");
		if(count($this->post('banned_word'))) {
			foreach ($this->post('banned_word') as $bw) {
				BannedWord::add($bw);
			}
		}
		Config::save('conversations.banned_words',!!$this->post('banned_list_enabled'));
		$this->redirect('dashboard/system/conversations/bannedwords/success');
	}

}
