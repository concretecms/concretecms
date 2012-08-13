<?
/**
*
* Responsible for loading the indexed search class and initiating the reindex command.
* @package Utilities
*/

defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Job_ProcessEmail extends Job {

	public function getJobName() {
		return t("Process Email Posts");
	}
	
	public function getJobDescription() {
		return t("Polls an email account and grabs private messages/postings that are sent there..");
	}
	
	public function run() {
		Loader::library('mail/importer');
		
		$list = MailImporter::getEnabledList();
		foreach($list as $mi) {
			// for each one, we connect and retrieve any mail messages we haven't seen
			$messages = $mi->getPendingMessages();
			foreach($messages as $me) {
				if ($me->validate()) {
					$mi->process($me);
					$mi->cleanup($me);
				} else {
					$mh = Loader::helper('mail');
					$mh->to($me->getOriginalSender());
					$mh->from($mi->getMailImporterEmail());
					$mh->addParameter('originalSubject', $me->getSubject());
					$mh->addParameter('error', $mi->getValidationErrorMessage());
					$mh->load('mail_importer_error');
					$mh->sendMail();
				}
			}
		}		
	}
}

?>