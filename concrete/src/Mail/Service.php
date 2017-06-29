<?php
namespace Concrete\Core\Mail;

use Concrete\Core\Application\Application;
use Concrete\Core\Entity\File\File;
use Concrete\Core\Logging\GroupLogger;
use Concrete\Core\Support\Facade\Application as ApplicationFacade;
use Exception;
use Monolog\Logger;
use Zend\Mail\Header\MessageId as MessageIdHeader;
use Zend\Mail\Message;
use Zend\Mail\Transport\TransportInterface;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Mime;
use Zend\Mime\Part as MimePart;

class Service
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * @var TransportInterface
     */
    protected $transport;

    protected $headers;
    protected $to;
    protected $replyto;
    protected $cc;
    protected $bcc;
    protected $from;
    protected $data;
    protected $subject;
    protected $attachments;
    protected $template;
    protected $body;
    protected $bodyHTML;
    protected $testing;

    /**
     * @param Application $app
     * @param TransportInterface $transport the mail transport to use to send emails
     */
    public function __construct(Application $app, TransportInterface $transport)
    {
        $this->app = $app;
        $this->transport = $transport;
        $this->reset();
    }

    /**
     * this method is called by the Loader::helper to clean up the instance of this object
     * resets the class scope variables.
     */
    public function reset()
    {
        $this->headers = [];
        $this->to = [];
        $this->replyto = [];
        $this->cc = [];
        $this->bcc = [];
        $this->from = [];
        $this->data = [];
        $this->subject = '';
        $this->attachments = [];
        $this->template = '';
        $this->body = false;
        $this->bodyHTML = false;
        $this->testing = false;
    }

    /**
     * Adds a parameter to a mail template.
     *
     * @param string $key
     * @param mixed $val
     */
    public function addParameter($key, $val)
    {
        $this->data[$key] = $val;
    }

    /**
     * Add a File entity as a mail attachment.
     *
     * @param File $fob File to attach
     */
    public function addAttachment(File $file)
    {
        $fileVersion = $file->getVersion();
        $resource = $fileVersion->getFileResource();
        $this->addRawAttachment(
            $resource->read(),
            $fileVersion->getFilename(),
            $fileVersion->getMimeType()
        );
    }

    /**
     * Add a mail attachment by specifying its raw binary data.
     *
     * @param string $content The binary data of the attachemt
     * @param string $filename The name to give to the attachment
     * @param string $mimetype The MIME type of the attachment
     */
    public function addRawAttachment($content, $filename, $mimetype = 'application/octet-stream')
    {
        $mp = new MimePart($content);
        $mp
            ->setType($mimetype)
            ->setDisposition(Mime::DISPOSITION_ATTACHMENT)
            ->setEncoding(Mime::ENCODING_BASE64)
            ->setFileName($filename);
        $this->attachments[] = $mp;
    }

    /**
     * Loads an email template from the /mail/ directory.
     *
     * @param string $template
     * @param string $pkgHandle
     */
    public function load($template, $pkgHandle = null)
    {
        extract($this->data);

        // loads template from mail templates directory
        if (file_exists(DIR_FILES_EMAIL_TEMPLATES . "/{$template}.php")) {
            include DIR_FILES_EMAIL_TEMPLATES . "/{$template}.php";
        } else {
            if ($pkgHandle != null) {
                if (is_dir(DIR_PACKAGES . '/' . $pkgHandle)) {
                    include DIR_PACKAGES . '/' . $pkgHandle . '/' . DIRNAME_MAIL_TEMPLATES . "/{$template}.php";
                } else {
                    include DIR_PACKAGES_CORE . '/' . $pkgHandle . '/' . DIRNAME_MAIL_TEMPLATES . "/{$template}.php";
                }
            } else {
                include DIR_FILES_EMAIL_TEMPLATES_CORE . "/{$template}.php";
            }
        }

        if (isset($from)) {
            $this->from($from[0], $from[1]);
        }
        $this->template = $template;
        $this->subject = $subject;
        $this->body = (isset($body) && is_string($body)) ? $body : false;
        $this->bodyHTML = (isset($bodyHTML) && is_string($bodyHTML)) ? $bodyHTML : false;
    }

    /**
     * Manually set the text body of a mail message, typically the body is set in the template + load method.
     *
     * @param string|false $body Set the text body (false to not use plain text body)
     */
    public function setBody($body)
    {
        $this->body = $body;
    }

    /**
     * Manually set the message's subject.
     *
     * @param string $subject
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    /**
     * Returns the message's subject.
     *
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Returns the message's text body.
     *
     * @return string|false
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Returns the message's html body.
     *
     * @return string|false
     */
    public function getBodyHTML()
    {
        return $this->bodyHTML;
    }

    /**
     * manually set the HTML portion of a MIME encoded message, can also be done by setting $bodyHTML in a mail template.
     *
     * @param string|false $html Set the html body (false to not use html body)
     */
    public function setBodyHTML($html)
    {
        $this->bodyHTML = $html;
    }

    /**
     * @param MailImporter $importer
     * @param array $data
     */
    public function enableMailResponseProcessing($importer, $data)
    {
        foreach ($this->to as $em) {
            $importer->setupValidation($em[0], $data);
        }
        $this->from($importer->getMailImporterEmail());
        $this->body = $importer->setupBody(($this->body === false) ? '' : $this->body);
    }

    /**
     * @param array $arr
     *
     * @return string
     *
     * @todo documentation
     */
    protected function generateEmailStrings($arr)
    {
        $str = '';
        for ($i = 0; $i < count($arr); ++$i) {
            $v = $arr[$i];
            if (isset($v[1])) {
                $str .= '"' . $v[1] . '" <' . $v[0] . '>';
            } elseif (isset($v[0])) {
                $str .= $v[0];
            }
            if (($i + 1) < count($arr)) {
                $str .= ', ';
            }
        }

        return $str;
    }

    /**
     * Sets the from address on the email about to be sent out.
     *
     * @param string $email
     * @param string $name
     */
    public function from($email, $name = null)
    {
        $this->from = [$email, $name];
    }

    /**
     * Sets to the to email address on the email about to be sent out.
     *
     * @param string $email
     * @param string $name
     */
    public function to($email, $name = null)
    {
        if (strpos($email, ',') > 0) {
            $email = explode(',', $email);
            foreach ($email as $em) {
                $this->to[] = [$em, $name];
            }
        } else {
            $this->to[] = [$email, $name];
        }
    }

    /**
     * Adds an email address to the cc field on the email about to be sent out.
     *
     * @param string $email
     * @param string $name
     *
     * @since 5.5.1
     */
    public function cc($email, $name = null)
    {
        if (strpos($email, ',') > 0) {
            $email = explode(',', $email);
            foreach ($email as $em) {
                $this->cc[] = [$em, $name];
            }
        } else {
            $this->cc[] = [$email, $name];
        }
    }

    /**
     * Adds an email address to the bcc field on the email about to be sent out.
     *
     * @param string $email
     * @param string $name
     *
     * @since 5.5.1
     */
    public function bcc($email, $name = null)
    {
        if (strpos($email, ',') > 0) {
            $email = explode(',', $email);
            foreach ($email as $em) {
                $this->bcc[] = [$em, $name];
            }
        } else {
            $this->bcc[] = [$email, $name];
        }
    }

    /*
     * Sets the reply-to address on the email about to be sent out
     * @param string $email
     * @param string $name
     * @return void
     */
    public function replyto($email, $name = null)
    {
        if (strpos($email, ',') > 0) {
            $email = explode(',', $email);
            foreach ($email as $em) {
                $this->replyto[] = [$em, $name];
            }
        } else {
            $this->replyto[] = [$email, $name];
        }
    }

    /** Set the testing state (if true the email logging never occurs and sending errors will throw an exception)
     * @param bool $testing
     */
    public function setTesting($testing)
    {
        $this->testing = $testing ? true : false;
    }

    /** Retrieve the testing state
     * @return bool
     */
    public function getTesting()
    {
        return $this->testing;
    }

    /**
     * Set additional headers.
     *
     * @param array $headers
     * @param string $val
     */
    public function setAdditionalHeaders($headers)
    {
        $this->headers = $headers;
    }

    /**
     * Sends the email.
     *
     * @param bool $resetData Whether or not to reset the service to its default values
     *
     * @throws Exception
     *
     * @return bool
     */
    public function sendMail($resetData = true)
    {
        $config = $this->app->make('config');
        $_from[] = $this->from;
        $fromStr = $this->generateEmailStrings($_from);
        $toStr = $this->generateEmailStrings($this->to);
        $replyStr = $this->generateEmailStrings($this->replyto);

        $mail = (new Message())->setEncoding(APP_CHARSET);

        if (is_array($this->from) && count($this->from)) {
            if ($this->from[0] != '') {
                $from = $this->from;
            }
        }
        if (!isset($from)) {
            $from = [$config->get('concrete.email.default.address'), $config->get('concrete.email.default.name')];
            $fromStr = $config->get('concrete.email.default.address');
        }

        // The currently included Zend library has a bug in setReplyTo that
        // adds the Reply-To address as a recipient of the email. We must
        // set the Reply-To before any header with addresses and then clear
        // all recipients so that a copy is not sent to the Reply-To address.
        if (is_array($this->replyto)) {
            foreach ($this->replyto as $reply) {
                $mail->setReplyTo($reply[0], $reply[1]);
            }
        }

        $mail->setFrom($from[0], $from[1]);
        $mail->setSubject($this->subject);
        foreach ($this->to as $to) {
            $mail->addTo($to[0], $to[1]);
        }

        if (is_array($this->cc) && count($this->cc)) {
            foreach ($this->cc as $cc) {
                $mail->addCc($cc[0], $cc[1]);
            }
        }

        if (is_array($this->bcc) && count($this->bcc)) {
            foreach ($this->bcc as $bcc) {
                $mail->addBcc($bcc[0], $bcc[1]);
            }
        }
        $headers = $mail->getHeaders();
        if ($headers->has('messageid')) {
            $messageIdHeader = $headers->get('messageid');
        } else {
            $messageIdHeader = new MessageIdHeader();
            $headers->addHeader($messageIdHeader);
        }

        $headers->addHeaders($this->headers);

        $messageIdHeader->setId();

        $body = new MimeMessage();
        if (($this->body !== false) && ($this->bodyHTML !== false)) {
            $alternatives = new MimeMessage();
            $text = new MimePart($this->body);
            $text->type = Mime::TYPE_TEXT;
            $text->charset = APP_CHARSET;
            $alternatives->addPart($text);
            $html = new MimePart($this->bodyHTML);
            $html->type = Mime::TYPE_HTML;
            $html->charset = APP_CHARSET;
            $alternatives->addPart($html);
            $alternativesPath = new MimePart($alternatives->generateMessage());
            $alternativesPath->charset = 'UTF-8';
            $alternativesPath->type = 'multipart/alternative';
            $alternativesPath->boundary = $alternatives->getMime()->boundary();
            $body->addPart($alternativesPath);
        } elseif ($this->body !== false) {
            $text = new MimePart($this->body);
            $text->type = 'text/plain';
            $text->charset = APP_CHARSET;
            $body->addPart($text);
        } elseif ($this->bodyHTML !== false) {
            $html = new MimePart($this->bodyHTML);
            $html->type = 'text/html';
            $html->charset = APP_CHARSET;
            $body->addPart($html);
        }
        foreach ($this->attachments as $att) {
            $body->addPart($att);
        }
        if (count($body->getParts()) === 0) {
            $text = new MimePart('');
            $text->type = 'text/plain';
            $text->charset = APP_CHARSET;
            $body->addPart($text);
        }
        $mail->setBody($body);

        $sent = false;
        try {
            if ($config->get('concrete.email.enabled')) {
                $this->transport->send($mail);
            }
            $sent = true;
        } catch (Exception $e) {
            if ($this->getTesting()) {
                throw $e;
            }
            $l = new GroupLogger(LOG_TYPE_EXCEPTIONS, Logger::CRITICAL);
            $l->write(t('Mail Exception Occurred. Unable to send mail: ') . $e->getMessage());
            $l->write($e->getTraceAsString());
            if ($config->get('concrete.log.emails')) {
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

        // add email to log
        if ($config->get('concrete.log.emails') && !$this->getTesting()) {
            $l = new GroupLogger(LOG_TYPE_EMAILS, Logger::INFO);
            if ($config->get('concrete.email.enabled')) {
                if ($sent) {
                    $l->write('**' . t('EMAILS ARE ENABLED. THIS EMAIL HAS BEEN SENT') . '**');
                } else {
                    $l->write('**' . t('EMAILS ARE ENABLED. THIS EMAIL HAS NOT BEEN SENT') . '**');
                }
            } else {
                $l->write('**' . t('EMAILS ARE DISABLED. THIS EMAIL WAS LOGGED BUT NOT SENT') . '**');
            }
            $l->write(t('Template Used') . ': ' . $this->template);
            $l->write(t('Mail Details: %s', $mail->toString()));
            $l->close();
        }

        // clear data if applicable
        if ($resetData) {
            $this->reset();
        }

        return $sent;
    }

    /**
     * @deprecated To get the mail transport, call \Core::make(\Zend\Mail\Transport\TransportInterface::class)
     */
    public static function getMailerObject()
    {
        $app = ApplicationFacade::getFacadeApplication();

        return [
            'mail' => (new Message())->setEncoding(APP_CHARSET),
            'transport' => $app->make(TransportInterface::class),
        ];
    }
}
