<?php

namespace Concrete\Core\Page\Command;

use Concrete\Core\Application\Application;
use Concrete\Core\Command\Task\Output\OutputAwareInterface;
use Concrete\Core\Command\Task\Output\OutputAwareTrait;
use Concrete\Core\Page\Page;

class UpdateStatisticsTrackersTaskCommandHandler implements OutputAwareInterface
{

    use OutputAwareTrait;

    /**
     * @var Application
     */
    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * @param UpdateStatisticsTrackersTaskCommand $command
     */
    public function __invoke(UpdateStatisticsTrackersTaskCommand $command)
    {
        $page = Page::getByID($command->getPageID());

        $tracker = $this->app->make('statistics/tracker');
        $tracker->track($page);

        $this->output->write(t('Updating tracker for page ID: %s', $command->getPageID()));

    }


}