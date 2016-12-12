<?php

namespace Concrete\Controller\SinglePage\Dashboard\System\Multilingual;

use Concrete\Controller\Panel\Multilingual;
use Concrete\Core\Foundation\Queue\Queue;
use Concrete\Core\Multilingual\Page\Section\Processor\MultilingualProcessorTarget;
use Concrete\Core\Multilingual\Page\Section\Processor\Processor;
use Concrete\Core\Multilingual\Page\Section\Section;
use \Concrete\Core\Page\Controller\DashboardPageController;

defined('C5_EXECUTE') or die("Access Denied.");

class Copy extends DashboardPageController
{

    public function view()
    {
        $this->set('pages', Section::getList());
    }

    public function tree_copied()
    {
        $this->set('message', t('Multilingual tree copied. You might consider rescanning links in the section you copied to.'));
        $this->view();
    }

    public function links_rescanned()
    {
        $this->set('message', t('Multilingual tree links rescanned.'));
        $this->view();
    }

    public function rescan_locale()
    {
        if ($this->token->validate('rescan_locale')) {
            $u = new \User();
            if ($u->isSuperUser()) {
                \Core::make('cache/request')->disable();
                $section = Section::getByID($_REQUEST['locale']);
                $target = new MultilingualProcessorTarget($section);
                $processor = new Processor($target);
                if ($_POST['process']) {
                    foreach($processor->receive() as $task) {
                        $processor->execute($task);
                    }
                    $obj = new \stdClass;
                    $obj->totalItems = $processor->getTotalTasks();
                    print json_encode($obj);
                    exit;
                } else {
                    $processor->process();
                }
                $totalItems = $processor->getTotalTasks();
                \View::element('progress_bar', array('totalItems' => $totalItems, 'totalItemsSummary' => t2("%d task", "%d tasks", $totalItems)));
                /*
                                $q = Queue::get('rescan_multilingual_section');
                                if ($_POST['process']) {
                                    $obj = new \stdClass;
                                    $messages = $q->receive(\Config::get('concrete.limits.copy_pages'));
                                    foreach($messages as $key => $p) {
                                        // delete the page here
                                        $page = unserialize($p->body);
                                        $oc = \Page::getByID($page['cID']);


                                        $q->deleteMessage($p);
                                    }

                                    $obj->totalItems = $q->count();
                                    print json_encode($obj);
                                    if ($q->count() == 0) {
                                        $q->deleteQueue('rescan_multilingual_section');
                                    }
                                    exit;

                                } else if ($q->count() == 0) {
                                    $oc = Section::getByID($_REQUEST['locale']);
                                    if (is_object($oc) && !$oc->isError()) {
                                        $oc->queueForDeletionRequest($q, false);
                                    }
                                }
                                $totalItems = $q->count();
                                \View::element('progress_bar', array('totalItems' => $totalItems, 'totalItemsSummary' => t2("%d page", "%d pages", $totalItems)));
                                */

                exit;
            }
        }
    }

}