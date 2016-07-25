<?php
namespace Concrete\Core\Mail\Importer\Type;

use Concrete\Core\Mail\Importer\MailImporter;
use Concrete\Core\User\PrivateMessage\PrivateMessage as UserPrivateMessage;
use UserInfo;

class PrivateMessage extends MailImporter
{
    public function process($mail)
    {
        // now that we're here, we know that we're validated and that this is an email
        // coming from someone proper.

        // We need to know what to do with it, now. We check the "data" column, which stores
        // a serialized PHP object that contains relevant information about what this item needs to respond to, post to, etc...
        $do = $mail->getDataObject();
        if ($do->msgID > 0) {
            $upm = UserPrivateMessage::getByID($do->msgID);
            if (is_object($upm)) {
                $originalTo = UserInfo::getByID($do->toUID);
                $originalFrom = UserInfo::getByID($do->fromUID);
                if (is_object($originalTo) && is_object($originalFrom)) {
                    $body = $mail->getProcessedBody();
                    $originalTo->sendPrivateMessage($originalFrom, $mail->getSubject(), $body, $upm);
                }
            }
        }
    }

    public function getValidationErrorMessage()
    {
        return t('Unable to process private message email. Check that your email contains the validation hash present in the original message. Your private message was NOT delivered.');
    }
}
