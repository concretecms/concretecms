<?php
namespace Concrete\Core\Conversation\Discussion;
use Loader;
use \Concrete\Core\Page\PageList;
use Page;
class DiscussionList extends PageList {

	public function __construct(Page $c) {
		$this->ignoreAliases();
		$this->filterByParentID($c->getCollectionID());
		$this->addToQuery('inner join CollectionVersionFeatureAssignments cva on cv.cID = cva.cID and cv.cvID = cva.cvID');
		$this->addToQuery('inner join ConversationFeatureDetailAssignments cda on cva.faID = cda.faID');
		$this->addToQuery('inner join Conversations cnv on cda.cnvID = cnv.cnvID');

	}

	public function sortByConversationDateLastMessage() {
		$this->sortBy('cnvDateLastMessage', 'desc');
	}

	public function sortByTotalReplies() {
		$this->sortBy('cnvMessagesTotal', 'desc');
	}

}
