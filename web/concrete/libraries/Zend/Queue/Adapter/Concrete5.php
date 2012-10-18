<?

defined('C5_EXECUTE') or die("Access Denied.");
require 'Zend/Queue/Adapter/Db.php';
class Zend_Queue_Adapter_Concrete5 extends Zend_Queue_Adapter_Db {
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
