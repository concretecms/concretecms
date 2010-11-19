<?php 
/**
 * @package Helpers
 * @category Concrete
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */

/**
 * Functions used to send mail in Concrete.
 * @package Helpers
 * @category Concrete
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */
 
defined('C5_EXECUTE') or die("Access Denied.");
class MailHelper {

	protected $headers = array();
	protected $to = array();
	protected $from = array();
	protected $data = array();
	protected $subject = '';
	public $body = '';
	protected $template; 
	protected $bodyHTML = false;
	
	public static function getMailerObject(){
		Loader::library('3rdparty/Zend/Mail');
		$response = array();
		$response['mail'] = new Zend_Mail(APP_CHARSET);
	
		if (MAIL_SEND_METHOD == "SMTP") {
			Loader::library('3rdparty/Zend/Mail/Transport/Smtp');
			$username = Config::get('MAIL_SEND_METHOD_SMTP_USERNAME');
			$password = Config::get('MAIL_SEND_METHOD_SMTP_PASSWORD');
			$port = Config::get('MAIL_SEND_METHOD_SMTP_PORT');
			$encr = Config::get('MAIL_SEND_METHOD_SMTP_ENCRYPTION');
			if ($username != '') {
				$config = array('auth' => 'login', 'username' => $username, 'password' => $password);
				if ($port != '') {
					$config['port'] = $port;
				}
				if ($encr != '') {
					$config['ssl'] = $encr;
				}
				$transport = new Zend_Mail_Transport_Smtp(Config::get('MAIL_SEND_METHOD_SMTP_SERVER'), $config);					
			} else {
				$transport = new Zend_Mail_Transport_Smtp(Config::get('MAIL_SEND_METHOD_SMTP_SERVER'));					
			}
			
			$response['transport']=$transport;
		}	
		
		return $response;		
	}
	
	/** 
	 * Adds a parameter to a mail template
	 * @param string $key
	 * @param string $val
	 * @return void
	 */
	public function addParameter($key, $val) {
		$this->data[$key] = $val;
	}
	
	/** 
	 * Loads an email template from the /mail/ directory
	 * @param string $template 
	 * @param string $pkgHandle 
	 * @return void
	 */
	public function load($template, $pkgHandle = null) {
		extract($this->data);

		// loads template from mail templates directory
		if (file_exists(DIR_FILES_EMAIL_TEMPLATES . "/{$template}.php")) {			
			include(DIR_FILES_EMAIL_TEMPLATES . "/{$template}.php");
		} else if ($pkgHandle != null) {
			if (is_dir(DIR_PACKAGES . '/' . $pkgHandle)) {
				include(DIR_PACKAGES . '/' . $pkgHandle . '/' . DIRNAME_MAIL_TEMPLATES . "/{$template}.php");
			} else {
				include(DIR_PACKAGES_CORE . '/' . $pkgHandle . '/' . DIRNAME_MAIL_TEMPLATES . "/{$template}.php");
			}
		} else {
			include(DIR_FILES_EMAIL_TEMPLATES_CORE . "/{$template}.php");
		}

		if (isset($from)) {
			$this->from($from[0], $from[1]);
		}
		$this->template = $template;
		$this->subject = $subject;
		$this->body = $body;
	}
	
	//if you don't want to use the load method
	public function setBody($body){
		$this->body = $body;
	}
	public function setSubject($subject){
		$this->subject = $subject;
	}	
	
	public function getSubject() {return $this->subject;}
	public function getBody() {return $this->body;}
	public function setBodyHTML($html) {
		$this->bodyHTML = $html;
	}	
	public function enableMailResponseProcessing($importer, $data) {
		foreach($this->to as $em) {
			$importer->setupValidation($em[0], $data);
		}
		$this->from($importer->getMailImporterEmail());
		$this->body = $importer->setupBody($this->body);		
	}
	
	protected function generateEmailStrings($arr) {
		$str = '';
		for ($i = 0; $i < count($arr); $i++) {
			$v = $arr[$i];
			if (isset($v[1])) {
				$str .= '"' . $v[1] . '" <' . $v[0] . '>';
			} else {
				$str .= $v[0];
			}
			if (($i + 1) < count($arr)) {
				$str .= ', ';
			}
		}
		return $str;
	}
	
	/** 
	 * Sets the from address on the email about to be sent out
	 * @param string $email
	 * @param string $name
	 * @return void
	 */
	public function from($email, $name = null) {
		$this->from = array($email, $name);
	}
	
	/** 
	 * Sets to the to email address on the email about to be sent out.
	 * @param string $email
	 * @param string $name
	 * @return void
	 */
	public function to($email, $name = null) {
		if (strpos($email, ',') > 0) {
			$email = explode(',', $email);
			foreach($email as $em) {
				$this->to[] = array($em, $name);
			}
		} else {
			$this->to[] = array($email, $name);	
		}
	}

	/*	
	 * Sets the reply-to address on the email about to be sent out
	 * @param string $email
	 * @param string $name
	 * @return void
	 */
	public function replyto($email, $name = null) {
		if (strpos($email, ',') > 0) {
			$email = explode(',', $email);
			foreach($email as $em) {
				$this->replyto[] = array($em, $name);
			}
		} else {
			$this->replyto[] = array($email, $name);	
		}
	}
		
	/** 
	 * Sends the email
	 */
	public function sendMail() {
		$_from[] = $this->from;
		$fromStr = $this->generateEmailStrings($_from);
		$toStr = $this->generateEmailStrings($this->to);
		$replyStr = $this->generateEmailStrings($this->replyto);
		if (ENABLE_EMAILS) {
			
			$zendMailData = self::getMailerObject();
			$mail=$zendMailData['mail'];
			$transport=(isset($zendMailData['transport']))?$zendMailData['transport']:NULL;
			
			if (is_array($this->from)) {
				if ($this->from[0] != '') {
					$from = $this->from;
				}
			}
			if (!isset($from)) {
				$from = array(EMAIL_DEFAULT_FROM_ADDRESS, EMAIL_DEFAULT_FROM_NAME);
			}
			
			// The currently included Zend library has a bug in setReplyTo that
			// adds the Reply-To address as a recipient of the email. We must
			// set the Reply-To before any header with addresses and then clear
			// all recipients so that a copy is not sent to the Reply-To address.
			if(is_array($this->replyto)) {
				foreach ($this->replyto as $reply) {
					$mail->setReplyTo($reply[0], $reply[1]);
				}
			}
			$mail->clearRecipients();
			

			$mail->setFrom($from[0], $from[1]);
			$mail->setSubject($this->subject);
			foreach($this->to as $to) {
				$mail->addTo($to[0], $to[1]);
			}
			$mail->setBodyText($this->body);
			if ($this->bodyHTML != false) {
				$mail->setBodyHTML($this->bodyHTML);
			}
			try {
				$mail->send($transport);
					
			} catch(Exception $e) {
				$l = new Log(LOG_TYPE_EXCEPTIONS, true, true);
				$l->write(t('Mail Exception Occurred. Unable to send mail: ') . $e->getMessage());
				$l->write($e->getTraceAsString());
				if (ENABLE_LOG_EMAILS) {
					$l->write(t('Template Used') . ': ' . $this->template);
					$l->write(t('To') . ': ' . $toStr);
					$l->write(t('From') . ': ' . $fromStr);
					if (isset($this->replyto)) {
						$l->write(t('Reply-To') . ': ' . $replyStr);
					}
					$l->write(t('Subject') . ': ' . $this->subject);
					$l->write(t('Body') . ': ' . $this->body);
				}				
				$l->close();
			}
		}
		
		// add email to log
		if (ENABLE_LOG_EMAILS) {
			$l = new Log(LOG_TYPE_EMAILS, true, true);
			if (ENABLE_EMAILS) {
				$l->write('**' . t('EMAILS ARE ENABLED. THIS EMAIL WAS SENT TO mail()') . '**');
			} else {
				$l->write('**' . t('EMAILS ARE DISABLED. THIS EMAIL WAS LOGGED BUT NOT SENT') . '**');
			}
			$l->write(t('Template Used') . ': ' . $this->template);
			$l->write(t('To') . ': ' . $toStr);
			$l->write(t('From') . ': ' . $fromStr);
			if (isset($this->replyto)) {
				$l->write(t('Reply-To') . ': ' . $replyStr);
			}
			$l->write(t('Subject') . ': ' . $this->subject);
			$l->write(t('Body') . ': ' . $this->body);
			$l->close();
		}		
	}
	
}

?>