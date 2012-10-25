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
	protected $jQueueBatchSize = 50;
	public function getJobQueueBatchSize() {return $this->jQueueBatchSize;}
	abstract public function start(Zend_Queue $q);
	abstract public function finish(Zend_Queue $q);
	abstract public function processQueueItem(Zend_Queue_Message $msg);
	public function run() {}
	
}