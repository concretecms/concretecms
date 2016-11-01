<?php
namespace Concrete\Core\Application;

use Core;
use stdClass;

class EditResponse implements \JsonSerializable
{
    public $time;
    public $message;
    public $redirectURL;
    public $title;
    protected $additionalData = array();
    public $error;

    public function setRedirectURL($url)
    {
        $this->redirectURL = $url;
    }

    public function getRedirectURL()
    {
        return $this->redirectURL;
    }

    public function __construct($e = false)
    {
        if ($e instanceof \Concrete\Core\Error\ErrorList\ErrorList && $e->has()) {
            $this->error = $e;
        } else {
            $this->error = Core::make('helper/validation/error');
        }
        $this->time = Core::make('helper/date')->toDB();
    }

    public function setError($error)
    {
        $this->error = $error;
    }

    public function setMessage($message)
    {
        $this->message = $message;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getJSON()
    {
        return json_encode($this->getJSONObject());
    }

    public function setAdditionalDataAttribute($key, $value)
    {
        $this->additionalData[$key] = $value;
    }

    public function getJSONObject()
    {
        return $this->getBaseJSONObject();
    }

    public function getBaseJSONObject()
    {
        $o = new stdClass();
        $o->message = $this->message;
        $o->title = $this->title;
        $o->time = $this->time;
        $o->redirectURL = (string) $this->redirectURL;
        foreach ($this->additionalData as $key => $value) {
            $o->{$key} = $value;
        }

        return $o;
    }

    public function jsonSerialize()
    {
        return $this->getJSONObject();
    }

    public function outputJSON()
    {
        if ($this->error && is_object($this->error) && (($this->error instanceof \Exception) || $this->error->has())) {
            Core::make('helper/ajax')->sendError($this->error);
        } else {
            Core::make('helper/ajax')->sendResult($this->getJSONObject());
        }
    }
}
