<?php

namespace Concrete\Block\PageTitle;

use Concrete\Core\Block\BlockController;
use Concrete\Core\Feature\Features;
use Concrete\Core\Feature\UsesFeatureInterface;
use Concrete\Core\Page\Page;
use Concrete\Core\Tree\Node\Type\Topic;

class Controller extends BlockController implements UsesFeatureInterface
{
    /**
     * @var string
     */
    public $titleText = '';

    /**
     * @var bool
     */
    public $useCustomTitle = false;

    /**
     * @var bool
     */
    public $useFilterTitle = false;

    /**
     * @var bool
     */
    public $useFilterTopic = false;

    /**
     * @var bool
     */
    public $useFilterTag = false;

    /**
     * @var bool
     */
    public $useFilterDate = false;

    /**
     * @var string
     */
    public $formatting = 'h1';

    /**
     * @var string|int
     */
    protected $btInterfaceWidth = 470;

    /**
     * @var string|int
     */
    protected $btInterfaceHeight = 500;

    /**
     * @var bool
     */
    protected $btCacheBlockOutput = true;

    /**
     * @var bool
     */
    protected $btCacheBlockOutputOnPost = true;

    /**
     * @var bool
     */
    protected $btCacheBlockOutputForRegisteredUsers = false;

    /**
     * @var string
     */
    protected $btTable = 'btPageTitle';

    /**
     * @var string
     */
    protected $btWrapperClass = 'ccm-ui';

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getBlockTypeDescription()
    {
        return t("Displays a Page's Title");
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getBlockTypeName()
    {
        return t('Page Title');
    }

    /**
     * @return string
     */
    public function getSearchableContent()
    {
        return $this->getTitleText();
    }

    /**
     * @return string
     */
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
            }
        }

        return $title ?? '';
    }

    /**
     * {@inheritdoc}
     *
     * @return string[]
     */
    public function getRequiredFeatures(): array
    {
        return [
            Features::BASICS,
        ];
    }

    /**
     * @return void
     */
    public function view()
    {
        if (empty($this->formatting)) {
            $this->set('formatting', 'h1');
        }
        $this->set('title', $this->getTitleText());
    }

    /**
     * @return void
     */
    public function on_start()
    {
        if ($this->useFilterTitle) {
            $this->btCacheBlockOutput = false;
            $this->btCacheBlockOutputOnPost = false;
        }
    }

    /**
     * {@inheritdoc}
     *
     * @param array<string, mixed> $data
     *
     * @return void
     */
    public function save($data)
    {
        if (!is_array($data)) {
            $data = [];
        }
        $data['useCustomTitle'] = isset($data['useCustomTitle']) && $data['useCustomTitle'] ? 1 : 0;
        $data['useFilterTitle'] = isset($data['useFilterTitle']) && $data['useFilterTitle'] ? 1 : 0;
        $data['useFilterTopic'] = isset($data['useFilterTopic']) && $data['useFilterTopic'] ? 1 : 0;
        $data['useFilterTag'] = isset($data['useFilterTag']) && $data['useFilterTag'] ? 1 : 0;
        $data['useFilterDate'] = isset($data['useFilterDate']) && $data['useFilterDate'] ? 1 : 0;

        parent::save($data);
    }

    /**
     * @param string|int|false $treeNodeID
     * @param false $topic
     *
     * @return void
     */
    public function action_topic($treeNodeID = false, $topic = false)
    {
        if ($treeNodeID) {
            $topicObj = Topic::getByID((int) $treeNodeID);
            if ($topicObj instanceof Topic) {
                $this->set('currentTopic', $topicObj);
            }
        }
        $this->view();
    }

    /**
     * @param bool|string|null $tag
     *
     * @return void
     */
    public function action_tag($tag = false)
    {
        if ($tag) {
            // the tag will be lowercase
            $this->set('tag', $tag);
        }
        $this->view();
    }

    /**
     * @param int|false $year
     * @param int|false $month
     *
     * @return void
     */
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

    /**
     * @param string[] $parameters
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     *
     * @return array<int,mixed>
     */
    public function getPassThruActionAndParameters($parameters)
    {
        if ($parameters[0] == 'topic') {
            $method = 'action_topic';
            $parameters = array_slice($parameters, 1);
        } elseif ($parameters[0] == 'tag') {
            $method = 'action_tag';
            $parameters = array_slice($parameters, 1);
        } elseif ($this->app->make('helper/validation/numbers')->integer($parameters[0])) {
            $method = 'action_date';
            $parameters[0] = (int) ($parameters[0]);
            if (isset($parameters[1])) {
                $parameters[1] = (int) ($parameters[1]);
            }
        } else {
            $parameters = $method = null;
        }

        return [$method, $parameters];
    }

    /**
     * @param string $title
     * @param bool $case
     *
     * @return string
     */
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

    /**
     * @param string $method
     * @param array<int,mixed> $parameters
     *
     * @return bool
     */
    public function isValidControllerTask($method, $parameters = [])
    {
        if (!$this->useFilterTitle) {
            return false;
        }

        if ($method === 'action_date') {
            // Parameter 0 must be set
            if (!isset($parameters[0]) || $parameters[0] < 0 || $parameters[0] > 9999) {
                return false;
            }
            // Parameter 1 can be null
            if (isset($parameters[1])) {
                if ($parameters[1] < 1 || $parameters[1] > 12) {
                    return false;
                }
            }
        }

        return parent::isValidControllerTask($method, $parameters);
    }
}
