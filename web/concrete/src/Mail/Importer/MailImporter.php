<?php
namespace Concrete\Core\Mail\Importer;
use Concrete\Core\Foundation\Object;
use Loader;
use Core;
use \Concrete\Core\Package\PackageList;
class MailImporter extends Object {

	/**
	 * gets the text string that's used to identify the body of the message
	 * @return string
	 */
	public function getMessageBodyHeader() {
		return t('--- Reply ABOVE. Do not alter this line --- [' . $this->validationHash  . '] ---');
	}

	public function getValidationHash() {
		return $this->validationHash;
	}
	

	public function getMessageBodyHashRegularExpression() {
		return t('/\-\-\- Reply ABOVE\. Do not alter this line \-\-\- \[(.*)\] \-\-\-/i');
	}

	public function getList() {
		$db = Loader::db();
		$r = $db->Execute('select miID from MailImporters order by miID asc');
		$importers = array();
		while ($row = $r->FetchRow()) {
			$importers[] = MailImporter::getByID($row['miID']);
		}
		return $importers;
	}

    /**
     * @return self[]
     */
    public function getEnabledList() {
		$db = Loader::db();
		$r = $db->Execute('select miID from MailImporters where miIsEnabled = 1 order by miID asc');
		$importers = array();
		while ($row = $r->FetchRow()) {
			$importers[] = MailImporter::getByID($row['miID']);
		}
		return $importers;
	}

    public static function getByID($miID) {
		$db = Loader::db();
		$row = $db->GetRow("select miID, miHandle, miServer, miUsername, miPassword, miEncryption, miIsEnabled, miEmail, miPort, miConnectionMethod, Packages.pkgID, pkgHandle from MailImporters left join Packages on MailImporters.pkgID = Packages.pkgID where miID = ?", array($miID));
		if (isset($row['miID'])) {
			$txt = Loader::helper('text');
			$mi = Core::make('\\Concrete\\Core\\Mail\\Importer\\Type\\' . $txt->camelcase($row['miHandle']));
			$mi->setPropertiesFromArray($row);
			return $mi;
		}
        return false;
	}

	public static function getByHandle($miHandle) {
		$db = Loader::db();
		$miID = $db->GetOne("select miID from MailImporters where miHandle = ?", $miHandle);
		return MailImporter::getByID($miID);
	}
	
	public function delete() {
		$db = Loader::db();
		$db->Execute('delete from MailImporters where miID = ?', array($this->miID));
	}
	
	public static function getListByPackage($pkg) {
		$db = Loader::db();
		$list = array();
		$r = $db->Execute('select miID from MailImporters where pkgID = ? order by miID asc', array($pkg->getPackageID()));
		while ($row = $r->FetchRow()) {
			$list[] = self::getByID($row['miID']);
		}
		$r->Close();
		return $list;
	}	
	
	public function getMailImporterID() {return $this->miID;}
	public function getMailImporterName() {
		$txt = Loader::helper('text');
		return $txt->unhandle($this->miHandle);
	}
	public function getMailImporterHandle() {return $this->miHandle;}
	public function getMailImporterServer() {return $this->miServer;}
	public function getMailImporterUsername() {return $this->miUsername;}
	public function getMailImporterPassword() {return $this->miPassword;}
	public function getMailImporterEncryption() {return $this->miEncryption;}
	public function getMailImporterEmail() {return $this->miEmail;}
	public function getMailImporterPort() {return $this->miPort;}
	public function isMailImporterEnabled() {return $this->miIsEnabled;}
	public function getMailImporterConnectionMethod() {return $this->miConnectionMethod;}
	public function getPackageID() { return $this->pkgID;}
	public function getPackageHandle() {
		return PackageList::getHandle($this->pkgID);
	}

	public function add($args, $pkg = null) {
		$db = Loader::db();
		extract($args);
		
		if ($miPort < 1) {
			$miPort = 0;
		}
		
		if ($miIsEnabled != 1) {
			$miIsEnabled = 0;
		}
		
		if ($miEncryption == '') {
			$miEncryption = null;
		}
		
		if (!$miConnectionMethod) {
			$miConnectionMethod = 'POP';
		}

		
		$pkgID = ($pkg == null) ? 0 : $pkg->getPackageID();
		
		$db->Execute('insert into MailImporters (miHandle, miServer, miUsername, miPassword, miEncryption, miIsEnabled, miEmail, miPort, miConnectionMethod, pkgID) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', 
			array(
				$miHandle,
				Loader::helper('security')->sanitizeString($miServer),
				$miUsername,
				$miPassword,
				$miEncryption,
				$miIsEnabled,
				Loader::helper('security')->sanitizeString($miEmail),
				$miPort, 
				$miConnectionMethod, 
				$pkgID
			));
		
		$miID = $db->Insert_ID();
		return MailImporter::getByID($miID);
	}
	
	public function update($args) {
		$db = Loader::db();
		extract($args);
		
		if ($miPort < 1) {
			$miPort = 0;
		}
		
		if ($miIsEnabled != 1) {
			$miIsEnabled = 0;
		}
		
		if ($miEncryption == '') {
			$miEncryption = null;
		}

		$db->Execute('update MailImporters set miServer = ?, miUsername = ?, miPassword = ?, miEncryption = ?, miIsEnabled = ?, miEmail = ?, miPort = ?, miConnectionMethod = ? where miID = ?', 
			array(
				Loader::helper('security')->sanitizeString($miServer),
				$miUsername,
				$miPassword,
				$miEncryption,
				$miIsEnabled,
				Loader::helper('security')->sanitizeString($miEmail),
				$miPort,
				$miConnectionMethod,
				$this->miID
			));
	}
	
	public function setupBody($body) {
		return $this->getMessageBodyHeader() . "\n\n" . $body;
	}

	public function getValidationErrorMessage() {
		return t('Unable to process email. Check that your email contains the validation hash present in the original message.');
	}

	public function setupValidation($email, $dataObject) {
		$db = Loader::db();
		$h = Loader::helper('validation/identifier');
		$hash = $h->generate('MailValidationHashes', 'mHash');
		$args = array($email, $this->miID, $hash, time(), serialize($dataObject));
		$db->Execute("insert into MailValidationHashes (email, miID, mHash, mDateGenerated, data) values (?, ?, ?, ?, ?)", $args);
		$this->validationHash = $hash;
		return $hash;
	}

    /**
     * @return MailImportedMessage[]
     */
    public function getPendingMessages() {
		$messages = array();
		// connect to the server to grab all messages 
		
		$args = array('host' => $this->miServer, 'user' => $this->miUsername, 'password' => $this->miPassword);
		if ($this->miEncryption != '') {
			$args['ssl'] = $this->miEncryption;
		}
		if ($this->miPort > 0) {
			$args['port'] = $this->miPort;
		}
		
		if ($this->miConnectionMethod == 'IMAP') {
			$mail = new Zend_Mail_Storage_Imap($args);
		} else { 
			$mail = new Zend_Mail_Storage_Pop3($args);
		}
		$i = 1;
		foreach($mail as $m) {
			$mim = new MailImportedMessage($mail, $m, $i);
			$messages[] = $mim;
			$i++;
		}
		
		return $messages;
	}

	public function cleanup(MailImportedMessage $me) {
		
		$db = Loader::db();
		$db->query("update MailValidationHashes set mDateRedeemed = " . time() . " where mHash = ?", array($me->getValidationHash()));
		
		$m = $me->getOriginalMailObject();
		$msg = $me->getOriginalMessageObject();
		$m->removeMessage($me->getOriginalMessageCount());


	}
	
}

class MailImportedMessage {
	
	protected $body;
	protected $subject;
	protected $sender;
	protected $validationHash;
	protected $oMail;
	protected $oMailMessage;
	protected $oMailCnt;
	
	public function __construct($mail, Zend_Mail_Message $msg, $count) {
		
		try {
			$this->subject = $msg->subject;
		} catch(Exception $e) {
		}
		
		$this->oMail = $mail;
		$this->oMailMessage = $msg;
		$this->oMailCnt = $count;
		
		if (strpos($msg->from, ' ') === false) {
			$this->sender = str_replace(array('>','<'), '', $msg->from);
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
					} catch (Zend_Mail_Exception $e) {
						// ignore
					}
				}
			
				if ($foundPart) {
					$this->body = quoted_printable_decode($foundPart);
				}
			}
		} catch(Exception $e) {
		
		}
		
		// find the hash
		$r = preg_match(MailImporter::getMessageBodyHashRegularExpression(), $this->body, $matches);
		if ($r) {
			$this->validationHash = $matches[1];
			if ($this->validationHash != '') {
				$db = Loader::db();
				$r = $db->GetOne('select data from MailValidationHashes where mHash = ?', array($this->validationHash));
				if ($r) {
					$this->dataObject = unserialize($r);
				}
			}
		}
	}
	
	public function getOriginalSender() {return $this->sender;}
	public function getOriginalMailObject() {return $this->oMail;}
	public function getOriginalMessageObject() {return $this->oMailMessage;}
	public function getOriginalMessageCount() {return $this->oMailCnt;}
	
	public function getSubject() {return $this->subject;}
	public function getBody() {return $this->body;}
	
	/** 
	 * Returns the relevant content of the email message, minus any quotations, and the line that includes the validation hash
	 */
	public function getProcessedBody() {
		$r = preg_split(MailImporter::getMessageBodyHashRegularExpression(), $this->body, $matches);		
		$message = $r[0];
		$r = preg_replace(array(
			'/^On (.*) at (.*), (.*) wrote:/sm',
			'/[\n\r\s\>]*\Z/i'
		), '', $message);
		return $r;
	}
	
	public function getValidationHash() {return $this->validationHash;}
	
	/** 
	 * Validates the email message - checks the validation hash found in the body with one in the database. Checks the from address as well
	 */
	public function validate() {
		if (!$this->validationHash) {
			return false;
		}
		$db = Loader::db();
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
	
	public function getDataObject() {return $this->dataObject;}	
	
	/** 
	 * checks to see if the message is a bounce or delivery failure
	 * @return boolean
	*/
	public function isSendError() {
		$message = $this->getOriginalMessageObject();
		$headers = $message->getHeaders();
		$isSendError = false;
		if(is_array($headers) && count($headers)) {
			foreach(array_keys($headers) as $key) {
				if(strstr($key, 'x-fail') !== false) {
					$isSendError = true; 
					break;
				}
			}
		}
		return $isSendError;
	}
}