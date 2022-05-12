<?php

namespace Concrete\Core\Application;

use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Support\Facade\Application as ApplicationFacade;
use Core;
use Exception;
use JsonSerializable;
use stdClass;

/**
 * The result of an edit operation.
 */
class EditResponse implements JsonSerializable
{
    /**
     * The error(s) of the response.
     *
     * @var \Concrete\Core\Error\ErrorList\ErrorList|\Exception|string|null
     *
     * @deprecated since concrete5 8.5.0a3 (what's deprecated is the "public part") - use setError/getError/hasError
     */
    public $error;

    /**
     * The date/time of the response in ISO-9075 format (YYYY-MM-DD hh:mm:ss).
     *
     * @var string
     *
     * @deprecated since concrete5 8.5.0a3 (what's deprecated is the "public part") - use getTime
     */
    public $time;

    /**
     * The title of the response.
     *
     * @var string|null
     *
     * @deprecated since concrete5 8.5.0a3 (what's deprecated is the "public part") - use setTitle/getTitle
     */
    public $title;

    /**
     * The message of the response.
     *
     * @var string|null
     *
     * @deprecated since concrete5 8.5.0a3 (what's deprecated is the "public part") - use setMessage/getMessage
     */
    public $message;

    /**
     * The redirect URL of the response.
     *
     * @var string|\League\URL\URLInterface|null
     *
     * @deprecated since concrete5 8.5.0a3 (what's deprecated is the "public part") - use setMessage/getMessage
     */
    public $redirectURL;

    /**
     * Additional response data.
     *
     * @var array
     */
    protected $additionalData = [];

    /**
     * @param \Concrete\Core\Error\ErrorList\ErrorList|\Exception|string|false $e
     */
    public function __construct($e = false)
    {
        $app = ApplicationFacade::getFacadeApplication();
        if ($e instanceof ErrorList) {
            $this->error = $e;
        } else {
            $this->error = $app->make('error');
            if ($e instanceof Exception) {
                $this->error->add($e);
            } else {
                $e = (string) $e;
                if ($e !== '') {
                    $this->error->add($e);
                }
            }
        }
        $this->time = $app->make('date')->toDB();
    }

    /**
     * Get the error(s) of the response.
     *
     * @return \Concrete\Core\Error\ErrorList\ErrorList|\Exception|string|null
     *
     * @since concrete5 8.5.0a3
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * Set the error(s) of the response.
     *
     * @param \Concrete\Core\Error\ErrorList\ErrorList|\Exception|string|null $error
     *
     * @return $this
     */
    public function setError($error)
    {
        $this->error = $error;

        return $this;
    }

    /**
     * Does this response contain an error?
     *
     * @return bool
     */
    public function hasError()
    {
        $error = $this->getError();
        if ($error instanceof ErrorList) {
            return $error->has();
        }
        if ($error instanceof Exception) {
            return true;
        }

        return (string) $error !== '';
    }

    /**
     * Get the date/time of the response in ISO-9075 format (YYYY-MM-DD hh:mm:ss).
     *
     * @return string
     *
     * @since concrete5 8.5.0a3
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * Set the title of the response.
     *
     * @param string|null $title
     *
     * @return $this
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get the title of the response.
     *
     * @return string|null
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set the message of the response.
     *
     * @param string|null $message
     *
     * @return $this
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get the message of the response.
     *
     * @return string|null
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set the redirect URL of the response.
     *
     * @param string|\League\URL\URLInterface|null $url
     *
     * @return $this
     */
    public function setRedirectURL($url)
    {
        $this->redirectURL = $url;

        return $this;
    }

    /**
     * Get the redirect URL of the response.
     *
     * @return string|\League\URL\URLInterface|null
     */
    public function getRedirectURL()
    {
        return $this->redirectURL;
    }

    /**
     * Set additional response data.
     *
     * @param string $key
     * @param mixed $value
     */
    public function setAdditionalDataAttribute($key, $value)
    {
        $this->additionalData[$key] = $value;
    }

    /**
     * Get the JSON representation of the data of this instance.
     *
     * @return string
     */
    public function getJSON()
    {
        return json_encode($this->getJSONObject());
    }

    /**
     * Get an stdClass instance containing the serialized data of this instance.
     *
     * @return \stdClass
     */
    public function getJSONObject()
    {
        return $this->getBaseJSONObject();
    }

    /**
     * Get an stdClass instance containing the serialized data of this instance.
     *
     * @return \stdClass
     */
    public function getBaseJSONObject()
    {
        $o = new stdClass();
        $error = $this->getError();
        if ($error instanceof Exception) {
            $o->error = true;
            $o->errors = [$error->getMessage()];
        } elseif ($error instanceof ErrorList && $error->has()) {
            $o->error = true;
            $o->errors = [];
            foreach ($error->getList() as $e) {
                $o->errors[] = (string) $e;
            }
        } else {
            $error = (string) $error;
            if ($error !== '') {
                $o->error = true;
                $o->errors = [$error];
            }
        }
        $o->time = $this->getTime();
        $o->message = $this->getMessage();
        $o->title = $this->getTitle();
        $o->redirectURL = (string) $this->getRedirectURL();
        foreach ($this->additionalData as $key => $value) {
            $o->{$key} = $value;
        }

        return $o;
    }

    /**
     * {@inheritdoc}
     *
     * @see \JsonSerializable::jsonSerialize()
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return $this->getJSONObject();
    }

    /**
     * @deprecated since concrete5 8.5.0a3 This method sends the response directly to the cliend and ends the execution: you should build a Response instance.
     */
    public function outputJSON()
    {
        if ($this->hasError()) {
            Core::make('helper/ajax')->sendError($this->error);
        } else {
            Core::make('helper/ajax')->sendResult($this->getJSONObject());
        }
    }
}
