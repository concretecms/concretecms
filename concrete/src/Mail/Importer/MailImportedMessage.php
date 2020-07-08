<?php
namespace Concrete\Core\Mail\Importer;

use Concrete\Core\Foundation\ConcreteObject;
use Core;
use Database;
use RecursiveIteratorIterator;
use Concrete\Core\Package\PackageList;
use Zend\Mail\Storage\Pop3    as MailStoragePop3;
use Zend\Mail\Storage\Imap    as MailStorageImap;
use Zend\Mail\Storage\Message as MailMessage;
use Zend\Mail\Exception       as MailException;

class MailImportedMessage
{
    protected $body;
    protected $subject;
    protected $sender;
    protected $validationHash;
    protected $oMail;
    protected $oMailMessage;
    protected $oMailCnt;

    public function __construct($mail, MailMessage $msg, $count)
    {
        try {
            $this->subject = $msg->subject;
        } catch (Exception $e) {
        }

        $this->oMail = $mail;
        $this->oMailMessage = $msg;
        $this->oMailCnt = $count;

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

    public function getSubject()
    {
        return $this->subject;
    }
    public function getBody()
    {
        return $this->body;
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
