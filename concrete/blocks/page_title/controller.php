<?php
namespace Concrete\Block\PageTitle;

use Page;
use Concrete\Core\Block\BlockController;
use Concrete\Core\Tree\Node\Type\Topic;
use Core;

class Controller extends BlockController
{
    protected $btInterfaceWidth = 470;
    protected $btInterfaceHeight = 500;
    protected $btCacheBlockOutput = true;
    protected $btCacheBlockOutputOnPost = true;
    protected $btCacheBlockOutputForRegisteredUsers = false;
    protected $btTable = 'btPageTitle';
    protected $btWrapperClass = 'ccm-ui';

    public function getBlockTypeDescription()
    {
        return t("Displays a Page's Title");
    }

    public function getBlockTypeName()
    {
        return t("Page Title");
    }

    public function getSearchableContent()
    {
        return $this->getTitleText();
    }

    public function getTitleText()
    {
        if ($this->useCustomTitle && strlen($this->titleText)) {
            $title = $this->titleText;
        } else {
            $p = Page::getCurrentPage();
            if ($p instanceof Page) {
                $title = $p->getCollectionName();
                if (!strlen($title) && $p->isMasterCollection()) {
                    $title = '[' . t('Page Title') . ']';
                }
            } else {
                $title = '';
            }
        }

        return $title;
    }

    public function view()
    {
        if (!(isset($this->formatting) && $this->formatting)) {
            $this->set('formatting', 'h1');
        }
        $this->set('title', $this->getTitleText());
    }

    public function on_start()
    {
        if ($this->useFilterTitle) {
            $this->btCacheBlockOutput = false;
            $this->btCacheBlockOutputOnPost = false;
        }
    }

    public function save($data)
    {
        $data['useCustomTitle'] = isset($data['useCustomTitle']) && $data['useCustomTitle'] ? 1 : 0;
        $data['useFilterTitle'] = isset($data['useFilterTitle']) && $data['useFilterTitle'] ? 1 : 0;
        $data['useFilterTopic'] = isset($data['useFilterTopic']) && $data['useFilterTopic'] ? 1 : 0;
        $data['useFilterTag'] = isset($data['useFilterTag']) && $data['useFilterTag'] ? 1 : 0;
        $data['useFilterDate'] = isset($data['useFilterDate']) && $data['useFilterDate'] ? 1 : 0;

        parent::save($data);
    }

    public function action_topic($treeNodeID = false, $topic = false)
    {
        if ($treeNodeID) {
            $topicObj = Topic::getByID(intval($treeNodeID));
            if ($topicObj instanceof Topic) {
                $this->set('currentTopic', $topicObj);
            }
        }
        $this->view();
    }

    public function action_tag($tag = false)
    {
        if ($tag) {
            // the tag will be lowercase
            $this->set('tag', $tag);
        }
        $this->view();
    }

    public function action_date($year = false, $month = false)
    {
        if ($year) {
            $this->set('year', $year);
        }
        if ($month) {
            $this->set('month', $month);
        }
        $this->view();
    }

    public function getPassThruActionAndParameters($parameters)
    {
        if ($parameters[0] == 'topic') {
            $method = 'action_topic';
            $parameters = array_slice($parameters, 1);
        } elseif ($parameters[0] == 'tag') {
            $method = 'action_tag';
            $parameters = array_slice($parameters, 1);
        } elseif (Core::make('helper/validation/numbers')->integer($parameters[0])) {
            $method = 'action_date';
            $parameters[0] = intval($parameters[0]);
            if (isset($parameters[1])) {
                $parameters[1] = intval($parameters[1]);
            }
        } else {
            $parameters = $method = null;
        }

        return array($method, $parameters);
    }

    public function formatPageTitle($title, $case = false)
    {
        switch ($case) {
            case 'lowercase':
                $title = mb_strtolower($title);
                break;
            case 'uppercase':
                $title = mb_strtoupper($title);
                break;
            case 'upperFirst':
                $title = mb_strtoupper(mb_substr($title, 0, 1)) . mb_strtolower(mb_substr($title, 1));
                break;
            case 'upperWord':
                $title = mb_convert_case($title, MB_CASE_TITLE);
                break;
        }

        return $title;
    }
}
