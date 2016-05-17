<?php
namespace Concrete\Job;

use Job as AbstractJob;
use Concrete\Core\Mail\Importer\MailImporter;
use Loader;

class ProcessEmail extends AbstractJob
{
    public function getJobName()
    {
        return t("Process Email Posts");
    }

    public function getJobDescription()
    {
        return t("Polls an email account and grabs private messages/postings that are sent there..");
    }

    public function run()
    {
        $mailImporter = new MailImporter();
        $list = $mailImporter->getEnabledList();
        foreach ($list as $mi) {
            // for each one, we connect and retrieve any mail messages we haven't seen
            $messages = $mi->getPendingMessages();
            foreach ($messages as $me) {
                if ($me->validate()) {
                    $mi->process($me);
                } elseif (!$me->isSendError()) {
                    $mh = Loader::helper('mail');
                    $mh->to($me->getOriginalSender());
                    $mh->from($mi->getMailImporterEmail());
                    $mh->addParameter('originalSubject', $me->getSubject());
                    $mh->addParameter('error', $mi->getValidationErrorMessage());
                    $mh->load('mail_importer_error');
                    $mh->sendMail();
                }
                $mi->cleanup($me);
            }
        }
    }
}
