<?php
namespace Concrete\Core\Utility;
use Zend_Queue_Adapter_Db;
use Zend_Queue;
use Zend_Db_Table_Abstract;
class ZendQueueAdapter extends Zend_Queue_Adapter_Db {
	public function __construct($options, Zend_Queue $queue = null) {
		parent::__construct($options, $queue);
		$this->_queueTable->setOptions(
			array(Zend_Db_Table_Abstract::NAME => 'Queues')
		);
		$this->_messageTable->setOptions(
			array(Zend_Db_Table_Abstract::NAME => 'QueueMessages')
		);
	}
}