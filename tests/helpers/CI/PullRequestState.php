<?php

namespace Concrete\TestHelpers\CI;

class PullRequestState extends State
{
    /**
     * The SHA-1 of the base commit (that is, the last commit of the base branch).
     *
     * @var string
     */
    private $baseSha1;

    /**
     * The SHA-1 of the head commit (that is, the last commit of the pull request branch).
     *
     * @var string
     */
    private $headSha1;

    /**
     * The SHA-1 of the merge commit.
     *
     * @var string
     */
    private $mergeSha1;

    /**
     * The SHA-1 of the most recent commit in common between the base commit and the head commit.
     *
     * @var string
     */
    private $commonCommitSha1;

    /**
     * @param string $engine the current engine (it's the value of one of the State::ENGINE__... constants)
     * @param string $event the event type (it's the value of one of the State::EVENT__... constants)
     * @param string $baseSha1 the SHA-1 of the base commit (that is, the last commit of the base branch)
     * @param string $headSha1 the SHA-1 of the head commit (that is, the last commit of the pull request branch)
     * @param string $mergeSha1 the SHA-1 of the merge commit
     * @param string $commonCommitSha1 the SHA-1 of the most recent commit in common between the base commit and the head commit
     */
    public function __construct(string $engine, string $event, string $baseSha1, string $headSha1, string $mergeSha1, string $commonCommitSha1)
    {
        parent::__construct($engine, $event);
        $this->baseSha1 = $baseSha1;
        $this->headSha1 = $headSha1;
        $this->mergeSha1 = $mergeSha1;
    }

    /**
     * Get the SHA-1 of the base commit (that is, the last commit of the base branch).
     */
    public function getBaseSha1(): string
    {
        return $this->baseSha1;
    }

    /**
     * Get the SHA-1 of the head commit (that is, the last commit of the pull request branch).
     */
    public function getHeadSha1(): string
    {
        return $this->headSha1;
    }

    /**
     * Get the SHA-1 of the merge commit.
     */
    public function getMergeSha1(): string
    {
        return $this->mergeSha1;
    }

    /**
     * Get the SHA-1 of the most recent commit in common between the base commit and the head commit.
     */
    public function getCommonCommitSha1(): string
    {
        return $this->commonCommitSha1;
    }
}
