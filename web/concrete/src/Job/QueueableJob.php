<?php
namespace Concrete\Core\Job;
use Config;
use \Job as AbstractJob;
use Queue;
use \ZendQueue\Queue as ZendQueue;
use \ZendQueue\Message as ZendQueueMessage;

abstract class QueueableJob extends AbstractJob {

	// optional queue functions
	protected $jQueueBatchSize;
	public function getJobQueueBatchSize() {return $this->jQueueBatchSize;}
	abstract public function start(ZendQueue $q);
	abstract public function finish(ZendQueue $q);
	abstract public function processQueueItem(ZendQueueMessage $msg);
	public function run() {}

    public function __construct() {
        $this->jQueueBatchSize = Config::get('concrete.limits.job_queue_batch');
    }

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
			$messages = $q->receive(PHP_INT_MAX);
			foreach($messages as $p) {
				$this->processQueueItem($p);
				$q->deleteMessage($p);
			}
			$result = $this->finish($q);
			$obj = $this->markCompleted(0, $result);
		} catch(\Exception $e) {
			$obj = $this->markCompleted(Job::JOB_ERROR_EXCEPTION_GENERAL, $e->getMessage());
			$obj->message = $obj->result; // needed for progressive library.
		}
		return $obj;
	}

}
