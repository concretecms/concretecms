<?php

namespace Concrete\Core\Logging;

use Concrete\Core\Support\Facade\Application;
use Concrete\Core\User\UserInfo;
use Concrete\Core\User\UserInfoRepository;
use DateTime;

class LogEntry
{
    /** @var int|null */
    protected $id;
    /** @var string|null */
    protected $channel;
    /** @var DateTime|null */
    protected $time;
    /** @var string|null */
    protected $message;
    /** @var string|null */
    protected $level;
    /** @var UserInfo|null */
    protected $user;

    public function __construct($row = null)
    {
        $app = Application::getFacadeApplication();
        /** @var UserInfoRepository $userInfoRepository */
        $userInfoRepository = $app->make(UserInfoRepository::class);

        if (is_array($row)) {
            if (isset($row["logID"])) {
                $this->setId($row["logID"]);
            }

            if (isset($row["channel"])) {
                $this->setChannel($row["channel"]);
            }

            if (isset($row["time"])) {
                $time = new DateTime();
                $time->setTimestamp($row["time"]);
                $this->setTime($time);
            }

            if (isset($row["message"])) {
                $this->setMessage($row["message"]);
            }

            if (isset($row["level"])) {
                $this->setLevel($row["level"]);
            }

            if (isset($row["uID"])) {
                $user = $userInfoRepository->getByID($row["uID"]);
                $this->setUser($user);
            }
        }
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     * @return LogEntry
     */
    public function setId(?int $id): LogEntry
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getChannel(): ?string
    {
        return $this->channel;
    }

    /**
     * @param string|null $channel
     * @return LogEntry
     */
    public function setChannel(?string $channel): LogEntry
    {
        $this->channel = $channel;
        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getTime(): ?DateTime
    {
        return $this->time;
    }

    /**
     * @param DateTime|null $time
     * @return LogEntry
     */
    public function setTime(?DateTime $time): LogEntry
    {
        $this->time = $time;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getMessage(): ?string
    {
        return $this->message;
    }

    /**
     * @param string|null $message
     * @return LogEntry
     */
    public function setMessage(?string $message): LogEntry
    {
        $this->message = $message;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getLevel(): ?string
    {
        return $this->level;
    }

    /**
     * @param string|null $level
     * @return LogEntry
     */
    public function setLevel(?string $level): LogEntry
    {
        $this->level = $level;
        return $this;
    }

    /**
     * @return UserInfo|null
     */
    public function getUser(): ?UserInfo
    {
        return $this->user;
    }

    /**
     * @param UserInfo|null $user
     * @return LogEntry
     */
    public function setUser(?UserInfo $user): LogEntry
    {
        $this->user = $user;
        return $this;
    }


}