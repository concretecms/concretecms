<?

defined('C5_EXECUTE') or die("Access Denied.");

/**
*
* Contains the job class.
* @package Utilities
* @author Andrew Embler <andrew@concrete5.org>
* @author Tony Trupp <tony@concrete5.org>
* @link http://www.concrete5.org
* @license http://www.opensource.org/licenses/mit-license.php MIT
*
*/

/**
*
* The job class is essentially sub-dispatcher for certain maintenance tasks that need to be run at specified intervals. Examples include indexing a search engine or generating a sitemap page.
* @package Utilities
* @author Andrew Embler <andrew@concrete5.org>
* @author Tony Trupp <tony@concrete5.org>
* @link http://www.concrete5.org
* @license http://www.opensource.org/licenses/mit-license.php MIT
*
*/
abstract class Concrete5_Model_QueueableJob extends Job {

	// optional queue functions
	protected $jQueueBatchSize = 100;
	public function getJobQueueBatchSize() {return $this->jQueueBatchSize;}
	abstract public function start(Zend_Queue $q);
	abstract public function finish(Zend_Queue $q);
	abstract public function processQueueItem(Zend_Queue_Message $msg);
	public function run() {}

	public function getQueueObject() {
		return Queue::get('job_' . $this->getJobHandle(), array('timeout' => 1));
	}

	public function reset() {
		parent::reset();
		$q = $this->getQueueObject();
		$q->deleteQueue();
	}

	public function markStarted() {
		parent::markStarted();
		return $this->getQueueObject();
	}

	public function markCompleted($code = 0, $message = false) {
		$obj = parent::markCompleted($code, $message);
		$q = $this->getQueueObject();
		if (!$this->didFail()) {
			$q->deleteQueue();
		}
		return $obj;
	}
	
	/** 
	 * Executejob for queueable jobs actually starts the queue, runs, and ends all in one function. This happens if we run a job in legacy mode.
	 */

	public function executeJob() {
		$q = $this->markStarted();
		$this->start($q);
		try {
			$messages = $q->receive(999999999999);
			foreach($messages as $key => $p) {
				$this->processQueueItem($p);
				$q->deleteMessage($p);
			}
			$result = $this->finish($q);
			$obj = $this->markCompleted(0, $result);
		} catch(Exception $e) {
			$obj = $this->markCompleted(Job::JOB_ERROR_EXCEPTION_GENERAL, $e->getMessage());
			$obj->message = $obj->result; // needed for progressive library.
		}
		return $obj;
	}

}