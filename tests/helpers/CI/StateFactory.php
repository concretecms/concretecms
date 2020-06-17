<?php

namespace Concrete\TestHelpers\CI;

class StateFactory
{
    /**
     * The value of the $_ENV global variable.
     *
     * @var array
     */
    private $env;

    /**
     * @param array|null $env the value of the $_ENV global variable
     */
    public function __construct(?array $env = null)
    {
        $this->env = $env === null ? getenv() : $env;
    }

    /**
     * @throws \Concrete\TestHelpers\CI\Exception
     */
    public function getState(): State
    {
        switch ($this->getEngine()) {
            case State::ENGINE_APPVEYOR:
                return $this->getAppVeyorState();
            case State::ENGINE_GITHUBACTIONS:
                return $this->getGitHubActionsState();
            case State::ENGINE_TRAVISCI:
                return $this->getTravisCIState();
        }
        throw new Exception('Unable to detect the CI engine');
    }

    protected function getEngine(): string
    {
        if (strcasecmp($this->getEnv('APPVEYOR'), 'true') === 0) {
            return State::ENGINE_APPVEYOR;
        }
        if (strcasecmp($this->getEnv('GITHUB_ACTIONS'), 'true') === 0) {
            return State::ENGINE_GITHUBACTIONS;
        }
        if (strcasecmp($this->getEnv('TRAVIS'), 'true') === 0) {
            return State::ENGINE_TRAVISCI;
        }

        return '';
    }

    protected function getAppVeyorState(): State
    {
        if ($this->getEnv('APPVEYOR_PULL_REQUEST_HEAD_COMMIT') !== '') {
            return $this->createPullRequestState(State::ENGINE_APPVEYOR);
        }
        $sha1 = $this->getEnv('APPVEYOR_REPO_COMMIT');
        if ($sha1 === '') {
            throw new Exception('AppVeyor without commit SHA-1');
        }
        if (strcasecmp($this->getEnv('APPVEYOR_REPO_TAG'), 'true') === 0) {
            $tag = $this->getEnv('APPVEYOR_REPO_TAG_NAME');
            if ($tag === '') {
                throw new Exception('AppVeyor tag event without tag name');
            }

            return new TagState(State::ENGINE_APPVEYOR, State::EVENT_TAG, $sha1, $tag);
        }
        if (strcasecmp($this->getEnv('APPVEYOR_SCHEDULED_BUILD'), 'true') === 0) {
            $event = State::EVENT_SCHEDULED;
        } elseif ($this->getEnv('APPVEYOR_FORCED_BUILD', 'true') === 0) {
            $event = State::EVENT_MANUAL;
        } else {
            $event = State::EVENT_PUSH;
        }

        return new SingleState(State::ENGINE_APPVEYOR, $event, $sha1);
    }

    protected function getGitHubActionsState(): State
    {
        $eventName = $this->getEnv('GITHUB_EVENT_NAME');
        if ($eventName === '') {
            throw new Exception('GitHub Actions without event name');
        }
        if ($eventName === 'pull_request') {
            return $this->createPullRequestState(State::ENGINE_GITHUBACTIONS);
        }
        $sha1 = $this->getEnv('GITHUB_SHA');
        if ($sha1 === '') {
            throw new Exception('GitHub Actions without commit SHA-1');
        }
        $matches = null;
        if ($eventName === 'create' && preg_match('%^refs/tags/(.+)%', $this->getEnv('GITHUB_REF'), $matches)) {
            return new TagState(State::ENGINE_GITHUBACTIONS, State::EVENT_TAG, $sha1, $matches[1]);
        }
        $eventMap = [
            'push' => State::EVENT_PUSH,
            'schedule' => State::EVENT_SCHEDULED,
            'repository_dispatch' => State::EVENT_MANUAL,
        ];
        $event = $eventMap[$eventName] ?? '';
        if ($event === '') {
            throw new Exception("Unrecognized GitHub Actions event name: '{$eventName}'");
        }

        return new SingleState(State::ENGINE_GITHUBACTIONS, $event, $sha1);
    }

    /**
     * @throws \Concrete\TestHelpers\CI\Exception
     */
    protected function getTravisCIState(): State
    {
        $eventType = $this->getEnv('TRAVIS_EVENT_TYPE');
        if ($eventType === '') {
            throw new Exception('TravisCI without event type');
        }
        if ($eventType === 'pull_request') {
            return $this->createPullRequestState(State::ENGINE_TRAVISCI);
        }
        $sha1 = $this->getEnv('TRAVIS_COMMIT');
        if ($sha1 === '') {
            throw new Exception('TravisCI without commit SHA-1');
        }
        $tag = $this->getEnv('TRAVIS_TAG');
        if ($tag !== '') {
            return new TagState(State::ENGINE_TRAVISCI, State::EVENT_TAG, $sha1, tag);
        }
        $eventMap = [
            'push' => State::EVENT_PUSH,
            'cron' => State::EVENT_SCHEDULED,
            'api' => State::EVENT_MANUAL,
        ];
        $event = $eventMap[$eventType] ?? '';
        if ($event === '') {
            throw new Exception("Unrecognized TravisCI event type: '{$eventType}'");
        }

        return new SingleState(State::ENGINE_TRAVISCI, $event, $sha1);
    }

    protected function getEnv(string $key, ?string $onUnset = ''): ?string
    {
        return $this->env[$key] ?? $onUnset;
    }

    /**
     * @throws \Concrete\TestHelpers\CI\Exception
     *
     * @return string[]
     */
    protected function runGit(string $args): array
    {
        $rc = -1;
        $output = [];
        exec('git -C ' . escapeshellarg(str_replace('/', DIRECTORY_SEPARATOR, DIR_BASE)) . ' ' . $args . ' 2>&1', $output, $rc);
        if ($rc !== 0) {
            throw new Exception("git command failed:\n" . trim(implode("\n", $output)));
        }

        return $output;
    }

    /**
     * @throws \Concrete\TestHelpers\CI\Exception
     */
    protected function createPullRequestState(string $engine): PullRequestState
    {
        $rawParents = $this->runGit('rev-list --parents -n1 HEAD');
        $parents = preg_split('/\s+/', $rawParents[0] ?? '', -1, PREG_SPLIT_NO_EMPTY);
        switch (count($parents)) {
            case 1:
                $mergeCommit = $parents[0];
                $commitMessageLines = $this->runGit('log --no-decorate --max-count=1 --format=%B');
                $matches = null;
                if (!isset($commitMessageLines[0]) || !preg_match('/^Merge ([0-9a-fA-F]{40}) into ([0-9a-fA-F]{40})$/', $commitMessageLines[0], $matches)) {
                    throw new Exception("Unexpected last commit message::\n" . trim(implode("\n", $commitMessageLines)));
                }
                $headSha1 = $matches[1];
                $baseSha1 = $matches[2];
                break;
            case 3:
                list($mergeCommit, $baseSha1, $headSha1) = $parents;
                break;
            default:
                throw new Exception("Failed to extract pull request parents from:\n" . trim(implode("\n", $rawParents)));
        }

        return new PullRequestState($engine, State::EVENT_PULLREQUEST, $baseSha1, $headSha1, $mergeCommit);
    }
}
