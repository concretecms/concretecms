<?

defined('C5_EXECUTE') or die("Access Denied.");

class PrivateMessageMailImporter extends MailImporter {
	
	public function process($mail) {
		// now that we're here, we know that we're validated and that this is an email
		// coming from someone proper.
		
		// We need to know what to do with it, now. We check the "data" column, which stores
		// a serialized PHP object that contains relevant information about what this item needs to respond to, post to, etc...
		$do = $mail->getDataObject();
		Loader::model('user_private_message');
		if ($do->msgID > 0) {
			$upm = UserPrivateMessage::getByID($do->msgID);
			if (is_object($upm)) {
				$originalTo = UserInfo::getByID($do->toUID);
				$originalFrom = UserInfo::getByID($do->fromUID);
				if (is_object($originalTo) && is_object($originalFrom)) {

					$body = $mail->getProcessedBody();
									
					Loader::library("file/importer");
					Loader::model('file_set');
					$fileSet = FileSet::getByName('Private Message Attachments');

					$attachments = array();
					$zm = $mail->getOriginalMessageObject();
					$i = 1;
	
					foreach (new RecursiveIteratorIterator($zm) as $part) { 
						if ($i > 1) { 
							$part = $zm->getPart($i);
							try {
								$fileName = $part->getHeader("content-description");
							} catch(Exception $e) { 
								$title = $part->getHeader('content-disposition');
								$r = preg_match('/filename=[\'"]?([^\'";]+)[\'"]?/', $title, $resp);
								$fileName = $resp[1];
							}
							
							$attachment = base64_decode($part->getContent());
							$savePath = DIR_TMP . '/' . Loader::helper('validation/identifier')->getString(32);
							$fh = fopen($savePath, 'w');
							fwrite($fh, $attachment);
							fclose($fh);
		
							if ($attachment) {
								$fi = new FileImporter();
								$obj = $fi->import($savePath, $fileName);
								if (is_object($obj)) {
									$fileSet->addFiletoSet($obj);
									$attachments[] = $obj;
								}
							}
						}
						$i++;
					}
					
					if (count($attachments) > 0) {
						$body .= "\n\n";
						foreach($attachments as $fo) { 
							$body .= "File Attachment: " . $fo->getFileName() . "\n";
							$body .= "Download URL: " . $fo->getDownloadURL() . "\n\n";
						}
					}

					$originalTo->sendPrivateMessage($originalFrom, $mail->getSubject(), $body, $upm);
				}
			}			
		}
	}
	
	public function getValidationErrorMessage() {
		return t('Unable to process private message email. Check that your email contains the validation hash present in the original message. Your private message was NOT delivered.');
	}
	

}