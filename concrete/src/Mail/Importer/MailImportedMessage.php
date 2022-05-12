<?php
namespace Concrete\Core\Mail\Importer;

use Concrete\Core\Foundation\ConcreteObject;
use Core;
use Database;
use RecursiveIteratorIterator;
use Concrete\Core\Package\PackageList;
use Laminas\Mail\Storage\Pop3                             as MailStoragePop3;
use Laminas\Mail\Storage\Imap                             as MailStorageImap;
use Laminas\Mail\Storage\Message                          as MailMessage;
use Laminas\Mail\Exception                                as MailException;
use Concrete\Core\Mail\Importer\MailImportedAttachment as Attachment;

class MailImportedMessage
{
    protected $body;
    protected $subject;
    protected $sender;
    protected $validationHash;
    protected $oMail;
    protected $oMailMessage;
    protected $oMailCnt;
    protected $oMailID;

    public function __construct($mail, MailMessage $msg, $count, $id)
    {

        $this->subject      = $msg->subject;
        $this->oMail        = $mail;
        $this->oMailMessage = $msg;
        $this->oMailCnt     = $count;
        $this->oMailID      = $id;

        if (strpos($msg->from, ' ') === false) {
            $this->sender = str_replace(array('>', '<'), '', $msg->from);
        } else {
            $this->sender = substr($msg->from, strpos($msg->from, '<') + 1, strpos($msg->from, '>') - strpos($msg->from, '<') - 1);
        }

        try {
            if (strpos($msg->contentType, 'text/plain') !== false) {
                $this->body = quoted_printable_decode($msg->getContent());
            } else {
                $foundPart = null;

                foreach (new RecursiveIteratorIterator($msg) as $part) {
                    try {
                        if (strtok($part->contentType, ';') == 'text/plain') {
                            $foundPart = $part;
                            break;
                        }
                    } catch (MailException $e) {
                        // ignore
                    }
                }

                if ($foundPart) {
                    $this->body = quoted_printable_decode($foundPart);
                }
            }
        } catch (Exception $e) {
        }

        // find the hash
        $r = preg_match(MailImporter::getMessageBodyHashRegularExpression(), $this->body, $matches);
        if ($r) {
            $this->validationHash = $matches[1];
            if ($this->validationHash != '') {
                $db = Database::connection();
                $r = $db->GetOne('select data from MailValidationHashes where mHash = ?', array($this->validationHash));
                if ($r) {
                    $this->dataObject = unserialize($r);
                }
            }
        }
    }

    public function getOriginalSender()
    {
        return $this->sender;
    }

    public function getOriginalMailObject()
    {
        return $this->oMail;
    }

    public function getOriginalMessageObject()
    {
        return $this->oMailMessage;
    }

    public function getOriginalMessageCount()
    {
        return $this->oMailCnt;
    }

    public function getOriginalMessageID()
    {
        return $this->oMailID;
    }

    public function getSubject()
    {
        return $this->subject;
    }

    public function getBody()
    {
        return $this->body;
    }

    protected function findAttachedFilesInPart($part)
    {
        $files = [];

        // Another multipart? Recursively search!
        if ($part->isMultipart()) {

            for ($i = 1; $i <= $part->countParts(); $i++) {
                $files = array_merge($files, $this->findAttachedFilesInPart($part->getPart($i)));
            }

        } else {
            $fileName    = null;
            $fileContent = $part->getContent();
            $fileType    = null;

            if ($part->getHeaders()->has('Content-Disposition')) {
                $params = explode(';', $part->getHeaders()->get('Content-Disposition')->getFieldValue());

                // Attachment and inline are okay!
                if ($params[0] == 'attachment' || $params[0] == 'inline') {

                    // Lookup filename
                    if (isset($params[1])) {
                        $fileName = trim($params[1]);
                    }


                    if ($part->getHeaders()->has('Content-Type')) {
                        $params   = explode(';', $part->getHeaders()->get('Content-Type')->getFieldValue());
                        $fileType = trim($params[0]);
                        $params   = $part->getHeaders()->get('Content-Type')->getParameters();

                        // Lookup filename (this is the preferred method)
                        if (isset($params['name'])) {
                            $fileName = $params['name'];
                        }

                    }

                    if ($part->getHeaders()->has('Content-Transfer-Encoding')) {

                        // Decode for the user
                        if ($part->getHeaders()->get('Content-Transfer-Encoding')->getFieldValue() == 'base64') {
                            $fileContent = base64_decode($fileContent);
                        }

                    }
                }
            }

            if ($fileName !== null) {
                $attachment = new Attachment($this);

                $attachment->setName($fileName);
                $attachment->setType($fileType);
                $attachment->setContent($fileContent);

                $files[] = $attachment;
            }

        }

        return $files;
    }

    /**
     * Return contents of attached files
     */
    public function getAttachments()
    {
        return $this->findAttachedFilesInPart($this->oMailMessage);
    }

    /**
     * Returns the relevant content of the email message, minus any quotations, and the line that includes the validation hash.
     */
    public function getProcessedBody()
    {
        $r = preg_split(MailImporter::getMessageBodyHashRegularExpression(), $this->body);
        $message = $r[0];
        $r = preg_replace(array(
            '/^On (.*) at (.*), (.*) wrote:/sm',
            '/[\n\r\s\>]*\Z/i',
        ), '', $message);

        return $r;
    }

    public function getValidationHash()
    {
        return $this->validationHash;
    }

    /**
     * Validates the email message - checks the validation hash found in the body with one in the database. Checks the from address as well.
     */
    public function validate()
    {
        if (!$this->validationHash) {
            return false;
        }
        $db = Database::connection();
        $row = $db->GetRow("select * from MailValidationHashes where mHash = ? order by mDateGenerated desc limit 1", $this->validationHash);
        if ($row['mvhID'] > 0) {
            // Can't do this even though it's a good idea
            //if ($row['email'] != $this->sender) {
            //	return false;
            //} else {
                return $row['mDateRedeemed'] == 0;
            //}
        }
    }

    /**
     * Deletes this mail by unique id
     */
    public function delete()
    {
        $this->oMail->removeMessage($this->oMail->getNumberByUniqueId($this->oMailID));
    }

    /**
     * Moves this mail by unique id (folder starts with '/')
     */
    public function moveTo($folder)
    {
        $this->oMail->moveMessage($this->oMail->getNumberByUniqueId($this->oMailID), $folder);
    }

    public function getDataObject()
    {
        return $this->dataObject;
    }

    /**
     * checks to see if the message is a bounce or delivery failure.
     *
     * @return bool
     */
    public function isSendError()
    {
        $message = $this->getOriginalMessageObject();
        $headers = $message->getHeaders();
        $isSendError = false;
        if (is_array($headers) && count($headers)) {
            foreach (array_keys($headers) as $key) {
                if (strstr($key, 'x-fail') !== false) {
                    $isSendError = true;
                    break;
                }
            }
        }

        return $isSendError;
    }
}
