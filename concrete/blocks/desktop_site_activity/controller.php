<?php

namespace Concrete\Block\DesktopSiteActivity;

use Concrete\Core\Api\ApiResourceValueInterface;
use Concrete\Core\Api\ApiValueSchemaInterface;
use Concrete\Core\Block\BlockController;
use Concrete\Core\Block\Traits\CustomApiValueTrait;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Feature\Features;
use Concrete\Core\Feature\UsesFeatureInterface;
use Concrete\Core\User\User;
use Concrete\Core\User\UserInfo;
use Concrete\Core\Workflow\Progress\Category;

defined('C5_EXECUTE') or die('Access Denied.');

class Controller extends BlockController implements ApiResourceValueInterface, ApiValueSchemaInterface, UsesFeatureInterface
{
    use CustomApiValueTrait;

    /**
     * @var int
     */
    protected $btInterfaceWidth = 450;

    /**
     * @var int
     */
    protected $btInterfaceHeight = 560;

    /**
     * @var string
     */
    protected $btTable = 'btDesktopSiteActivity';

    /**
     * @var string|null
     */
    protected $types;

    /**
     * @return array|string[]
     */
    public function getRequiredFeatures(): array
    {
        return [Features::DESKTOP];
    }

    /**
     * Get the kinds of activity that can be displayed.
     *
     * @return array<string,string> the keys are the identifiers of the kinds, the values are their names
     */
    protected function getAvailableTypes(): array
    {
        return [
            'form_submissions' => t('Form Submissions'),
            'survey_results' => t('Survey Results'),
            'signups' => t('New Users'),
            'conversation_messages' => t('Conversation Messages'),
            'workflow' => t('Workflow Approvals'),
        ];
    }

    /**
     * @return string
     */
    public function getBlockTypeDescription()
    {
        return t('Displays a graph of recent activity on your site.');
    }

    /**
     * @return string
     */
    public function getBlockTypeName()
    {
        return t('Site Activity');
    }

    /**
     * @param array<string,mixed> $args
     *
     * @return void
     */
    public function save($args)
    {
        $types = json_encode($args['types'] ?? []);
        parent::save(['types' => $types]);
    }

    /**
     * @return void
     */
    public function add()
    {
        $this->set('availableTypes', $this->getAvailableTypes());
        $this->set('types', []);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Api\ApiValueSchemaInterface::getApiValueSchema()
     */
    public function getApiValueSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'types' => [
                    'type' => 'array',
                    'description' => 'The kinds of activity to be displayed.',
                    'items' => [
                        'type' => 'string',
                        'enum' => array_keys($this->getAvailableTypes()),
                    ],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Block\BlockController::getImportDataFromApiValue()
     */
    public function getImportDataFromApiValue($page, array $value): array
    {
        if (!array_key_exists('types', $value)) {
            $value = $this->bID ? $this->serializeValueForApi() : ['types' => []];
        }
        $args = ['types' => []];
        foreach ((array) $value['types'] as $type) {
            if (is_string($type)) {
                $args['types'][] = $type;
            }
        }

        return $args;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Block\Traits\CustomApiValueTrait::serializeValueForApi()
     */
    protected function serializeValueForApi(): array
    {
        // the export() method below writes the kinds as the children of the <data> element, which is not
        // the list of records of a database table that the API knows how to read
        $blockNode = new \SimpleXMLElement('<block></block>');
        $this->export($blockNode);
        $types = [];
        foreach ($blockNode->data->type as $type) {
            $types[] = (string) $type;
        }

        return ['types' => $types];
    }

    /**
     * @param \SimpleXMLElement $blockNode
     *
     * @return void
     */
    public function export(\SimpleXMLElement $blockNode)
    {
        $data = $blockNode->addChild('data');
        $this->loadTypes();
        $types = $this->get('types');
        foreach ($types as $type) {
            $data->addChild('type', $type);
        }
    }

    /**
     * @param \SimpleXMLElement $blockNode
     * @param \Concrete\Core\Page\Page $page
     *
     * @return array<string, mixed>|mixed[]
     */
    public function getImportData($blockNode, $page)
    {
        $args = [];
        foreach ($blockNode->data->type as $type) {
            $args['types'][] = (string) $type;
        }

        return $args;
    }

    /**
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Doctrine\DBAL\Exception
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     *
     * @return void
     */
    public function view()
    {
        $this->loadTypes();
        $types = $this->get('types');
        $u = $this->app->make(User::class);
        $ui = UserInfo::getByID($u->getUserID());

        if (in_array('signups', $types)) {
            $signups = $this->getLatestSignups($ui->getLastLogin());
            $this->set('signups', $signups);
        }
        if (in_array('form_submissions', $types)) {
            $submissions = $this->getLatestFormSubmissions($ui->getLastLogin());
            $this->set('formResults', $submissions);
        }
        if (in_array('survey_results', $types)) {
            $results = $this->getLatestSurveyResults($ui->getLastLogin());
            $this->set('surveyResults', $results);
        }
        if (in_array('conversation_messages', $types)) {
            $messages = $this->getLatestMessages($ui->getLastLogin());
            $this->set('messages', $messages);
        }
        if (in_array('workflow', $types)) {
            $approvals = $this->getWorkflowProgressItems();
            $this->set('approvals', $approvals);
        }
    }

    /**
     * @return void
     */
    public function edit()
    {
        $this->set('availableTypes', $this->getAvailableTypes());
        $this->loadTypes();
    }

    /**
     * @return void
     */
    protected function loadTypes()
    {
        $types = @json_decode($this->types);
        if (!is_array($types)) {
            $types = [];
        }
        $this->set('types', $types);
    }

    /**
     * @param int $since
     *
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Doctrine\DBAL\Exception
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     *
     * @return false|int
     */
    protected function getLatestSignups($since)
    {
        /** @var Connection $db */
        $db = $this->app->make(Connection::class);
        $r = $db->executeQuery('select count(uID) from Users where UNIX_TIMESTAMP(uDateAdded) >= ? and uIsActive = 1', [$since]);

        return $r->fetchOne();
    }

    /**
     * @param int $since
     *
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Doctrine\DBAL\Exception
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     *
     * @return false|int
     */
    protected function getLatestSurveyResults($since)
    {
        /** @var Connection $db */
        $db = $this->app->make(Connection::class);
        $r = $db->executeQuery('select count(uID) from btSurveyResults where UNIX_TIMESTAMP(timestamp) >= ?', [$since]);

        return $r->fetchOne();
    }

    /**
     * @param int $since
     *
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Doctrine\DBAL\Exception
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     *
     * @return false|int
     */
    protected function getLatestMessages($since)
    {
        /** @var Connection $db */
        $db = $this->app->make(Connection::class);
        $r = $db->executeQuery('select count(uID) from ConversationMessages where UNIX_TIMESTAMP(cnvMessageDateCreated) >= ?', [$since]);

        return $r->fetchOne();
    }

    /**
     * @param int $since
     *
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Doctrine\DBAL\Exception
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     *
     * @return int
     */
    protected function getLatestFormSubmissions($since)
    {
        $since = date('Y-m-d H:i:s', $since);

        // legacy
        /** @var Connection $db */
        $db = $this->app->make(Connection::class);
        $r = $db->executeQuery('select count(uID) from btFormAnswerSet where created >= ?', [$since]);
        $legacy = $r->fetchOne();

        // new
        $entityManager = $db->getEntityManager();
        $forms = $entityManager->getRepository('Concrete\Core\Entity\Express\Entity')
            ->findExpressForms()
        ;

        $ids = [];
        $new = 0;
        foreach ($forms as $form) {
            $ids[] = $form->getID();
        }

        if (count($ids)) {
            $q = $entityManager->createQuery(
                'select count(e) from Concrete\Core\Entity\Express\Entry e where e.entity in (:entities) and e.exEntryDateCreated >= :date'
            );
            $q->setParameter('entities', $forms);
            $q->setParameter('date', $since);
            $new = $q->getSingleScalarResult();
        }

        return $legacy + $new;
    }

    /**
     * @return int
     */
    protected function getWorkflowProgressItems()
    {
        $categories = Category::getList();
        $items = 0;
        foreach ($categories as $category) {
            $list = $category->getPendingWorkflowProgressList();
            if (is_object($list)) {
                foreach ($list->get() as $it) {
                    $wp = $it->getWorkflowProgressObject();
                    $wf = $wp->getWorkflowObject();
                    if ($wf->canApproveWorkflowProgressObject($wp)) {
                        $items++;
                    }
                }
            }
        }

        return $items;
    }
}
