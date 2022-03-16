<?php
namespace Concrete\Core\Mail\Command;

use Concrete\Core\Mail\Importer\MailImporter;

class ProcessEmailCommandHandler
{

    public function __invoke(ProcessEmailCommand $command)
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
