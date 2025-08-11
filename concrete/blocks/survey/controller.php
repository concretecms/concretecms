<?php

namespace Concrete\Block\Survey;

use Concrete\Core\Block\BlockController;
use Concrete\Core\Cookie\CookieJar;
use Concrete\Core\Cookie\ResponseCookieJar;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Feature\Features;
use Concrete\Core\Feature\UsesFeatureInterface;
use Concrete\Core\Routing\RedirectResponse;
use Concrete\Core\User\User;
use Core;
use Database;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\EntityManagerInterface;
use Page;

class Controller extends BlockController implements UsesFeatureInterface
{
    public $options = [];

    protected $btTable = 'btSurvey';

    protected $btInterfaceWidth = 500;

    protected $btInterfaceHeight = 500;

    protected $btExportTables = ['btSurvey', 'btSurveyOptions', 'btSurveyResults'];

    public $question;

    public $showResults;

    public $customMessage;

    public $cID;

    public $requiresRegistration = false;
    /**
     * Used for localization. If we want to localize the name/description we have to include this.
     */
    public function getBlockTypeDescription()
    {
        return t('Provide a simple survey, along with results in a pie chart format.');
    }

    public function getBlockTypeName()
    {
        return t('Survey');
    }

    public function getQuestion()
    {
        return $this->question;
    }

    public function getShowResults()
    {
        return $this->showResults;
    }

    public function getCustomMessage()
    {
        return $this->customMessage;
    }

    public function getPollOptions()
    {
        return $this->options;
    }

    public function getRequiredFeatures(): array
    {
        return [
            Features::POLLS,
        ];
    }

    public function setPollOptions()
    {
        $this->cID = null;

        $c = Page::getCurrentPage();

        if (is_object($c)) {
            $this->cID = $c->getCollectionID();
        }
        if ($this->bID) {
            $db = Database::connection();
            $v = [$this->bID];
            $q = 'SELECT optionID, optionName, displayOrder FROM btSurveyOptions WHERE bID = ? ORDER BY displayOrder ASC';
            $r = $db->query($q, $v);
            $this->options = [];
            if ($r) {
                while ($row = $r->fetch()) {
                    $opt = new Option();
                    $opt->optionID = $row['optionID'];
                    $opt->cID = $this->cID;
                    $opt->optionName = $row['optionName'];
                    $opt->displayOrder = $row['displayOrder'];
                    $this->options[] = $opt;
                }
            }
        }
    }

    public function edit()
    {
        $this->setPollOptions();
    }

    public function view()
    {
        $this->setPollOptions();
    }

    public function delete()
    {
        $db = Database::connection();
        $v = [$this->bID];

        $q = 'DELETE FROM btSurveyOptions WHERE bID = ?';
        $db->query($q, $v);

        $q = 'DELETE FROM btSurveyResults WHERE bID = ?';
        $db->query($q, $v);

        parent::delete();
    }

    public function action_form_save_vote($bID = false)
    {
        if ($this->bID != $bID) {
            return false;
        }

        $u = $this->app->make(User::class);
        $db = Database::connection();
        $bo = $this->getBlockObject();
        if ($this->request->request->has('rcID')) {
            // we pass the rcID through the form so we can deal with stacks
            $c = Page::getByID($this->request->request->get('rcID'));
        } else {
            $c = $this->getCollectionObject();
        }

        if (is_object($c)) {
            $this->cID = $c->getCollectionID();
        }

        if ($this->requiresRegistration()) {
            if (!$u->isRegistered()) {
                $this->redirect('/login');
            }
        }

        if (!$this->hasVoted()) {
            $antispam = Core::make('helper/validation/antispam');
            if ($antispam->check('', 'survey_block')) { // we do a blank check which will still check IP and UserAgent's
                $duID = 0;
                if ($u->getUserID() > 0) {
                    $duID = $u->getUserID();
                }

                /** @var \Concrete\Core\Permission\IPService $iph */
                $iph = Core::make('helper/validation/ip');
                $ip = $iph->getRequestIP();
                $ip = ($ip === false) ? ('') : ($ip->getIp($ip::FORMAT_IP_STRING));
                $v = [
                    $this->request->get('optionID'),
                    $this->bID,
                    $duID,
                    $ip,
                    $this->cID,
                ];
                $q = 'INSERT INTO btSurveyResults (optionID, bID, uID, ipAddress, cID) VALUES (?, ?, ?, ?, ?)';
                $db->query($q, $v);
                $cookieJar = $this->app->make(ResponseCookieJar::class);
                $cookieKey = 'ccmPoll' . $this->bID . '-' . $this->cID;
                $config = $this->app->make('config');
                $cookieJar->addCookie(
                    $cookieKey,
                    'voted',
                    time() + 1296000,
                    DIR_REL . '/',
                    // $domain
                    $config->get('concrete.session.cookie.cookie_domain'),
                    // $secure
                    $config->get('concrete.session.cookie.cookie_secure'),
                    // $httpOnly
                    $config->get('concrete.session.cookie.cookie_httponly')
                );
                return new RedirectResponse($c->getCollectionPath() . '?survey_voted=1');
            }
        }
    }

    public function requiresRegistration()
    {
        return $this->requiresRegistration;
    }

    public function hasVoted()
    {
        $u = $this->app->make(User::class);
        $cookieJar = $this->app->make(CookieJar::class);
        $cookieKey = 'ccmPoll' . $this->bID . '-' . $this->cID;
        $cookieHasVoted = false;
        if ($cookieJar->has($cookieKey) && $cookieJar->get($cookieKey) == 'voted') {
            $cookieHasVoted = true;
        }
        if ($u->isRegistered()) {
            $db = Database::connection();
            $v = [$u->getUserID(), $this->bID, $this->cID];
            $q = 'SELECT count(resultID) AS total FROM btSurveyResults WHERE uID = ? AND bID = ? AND cID = ?';
            $result = $db->getOne($q, $v);
            if ($result > 0) {
                return true;
            }
        } else if ($cookieHasVoted) {
            return true;
        }

        return false;
    }

    public function duplicate($newBID)
    {
        $this->setPollOptions();
        /** @var \Concrete\Core\Database\Connection\Connection $db */
        $db = $this->app->make('database/connection');

        /** @var Option[] $opt */
        foreach ($this->options as $opt) {
            $db->insert('btSurveyOptions', ['bID' => $newBID, 'optionName' => $opt->getOptionName(), 'displayOrder' => $opt->getOptionDisplayOrder()]);
            $newOptionID = $db->lastInsertId();
            $results = $db->executeQuery('SELECT uID, ipAddress, timestamp FROM btSurveyResults WHERE optionID = :optID', ['optID' => $newOptionID])->fetchAll();
            if (!empty($results)) {
                foreach ($results as $row) {
                    $db->insert('btSurveyResults', ['optionID' => $newOptionID, 'uID' => $row['uID'], 'ipAddress' => $row['ipAddress'], 'timestamp' => $row['timestamp']]);
                }
            }
        }

        return parent::duplicate($newBID);
    }

    /**
     * Validates the survey block data, requiring at least survey options.
     *
     * @param array|string|null $args
     *
     * @version 9.0.0a3 Method added for survey block
     *
     * @return ErrorList
     */
    public function validate($args)
    {
        /** @var ErrorList $e */
        $e = $this->app->make(ErrorList::class);
        $sanitizer = $this->app->make('helper/security');

        if (!isset($args['question']) || empty($sanitizer->sanitizeString($args['question']))) {
            $e->addError(t('Question must not be blank.'), false, 'question');
        }

        if ((!isset($args['survivingOptionNames']) || !is_array($args['survivingOptionNames'])) && (!isset($args['pollOption']) || !is_array($args['pollOption']))) {
            $e->addError(t('Survey must have at least 1 answer.'), false, 'optionValue');
        }

        return $e;
    }

    public function save($args)
    {
        $sanitizer = $this->app->make('helper/security');
        if (empty($args['showResults'])) {
            $args['showResults'] = 0;
            $args['customMessage'] = '';
        }

        $args['customMessage'] = $sanitizer->sanitizeString($args['customMessage']);

        $args['question'] = $sanitizer->sanitizeString($args['question']);

        parent::save($args);
        /** @var \Concrete\Core\Database\Connection\Connection $db */
        $db = $this->app->make('database/connection');

        if (!isset($args['survivingOptionNames']) || !is_array($args['survivingOptionNames'])) {
            $args['survivingOptionNames'] = [];
        }

        $sanitizedArgs = [];

        foreach ($args['survivingOptionNames'] as $arg) {
            $sanitizedArgs[] = $sanitizer->sanitizeString($arg);
        }
        $queryBuilder = $db->createQueryBuilder();

        $queryBuilder->delete('btSurveyOptions')
            ->andWhere($queryBuilder->expr()->eq('bID', ':blockID'))
            ->setParameter('blockID', (int) $this->bID, Types::INTEGER)
        ;
        if (!empty($sanitizedArgs)) {
            $queryBuilder->andWhere($queryBuilder->expr()->notIn('optionName', ':names'))->setParameter('names', $sanitizedArgs, Connection::PARAM_STR_ARRAY);
        }
        $queryBuilder->execute();

        $max = $db->fetchColumn(
            'SELECT MAX(displayOrder) AS maxDisplayOrder FROM btSurveyOptions WHERE bID = :bID',
            ['bID' => (int) $this->bID]
        );

        $displayOrder = $max ? (int) $max + 1 : 0;

        if (isset($args['pollOption']) && is_array($args['pollOption'])) {
            $db->beginTransaction();
            foreach ($args['pollOption'] as $optionName) {
                $optionName = $sanitizer->sanitizeString($optionName);
                // Dont add if the sanitized string is empty
                if (!empty($optionName)) {
                    $db->insert('btSurveyOptions', ['bID' => (int) $this->bID, 'optionName' => $optionName, 'displayOrder' => $displayOrder]);
                    $displayOrder++;
                }
            }
            $db->commit();
        }

        $queryBuilder = $db->createQueryBuilder();
        $queryBuilder->delete('btSurveyResults')->where($queryBuilder->expr()->notIn(
            'optionID',
            'SELECT optionID from btSurveyOptions WHERE bID = :bID'
        ))->andWhere($queryBuilder->expr()->eq('bID', ':bID'))->setParameter('bID', (int) $this->bID, Types::INTEGER)->execute();
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Block\BlockController::export()
     */
    public function export(\SimpleXMLElement $blockNode)
    {
        $em = $this->app->make(EntityManagerInterface::class);
        parent::export($blockNode);
        foreach (($blockNode->xpath('./data[@table="btSurveyResults"]/record') ?: []) as $resultNode) {
            unset($resultNode->resultID[0]);
            $uID = isset($resultNode->uID) ? (int) $resultNode->uID : 0;
            $u = $uID > 0 ? $em->find(\Concrete\Core\Entity\User\User::class, $uID) : 0;
            $resultNode->uID = $u ? "user:{$u->getUserName()}" : '';
            $cID = isset($resultNode->cID) ? (int) $resultNode->cID : 0;
            $p = $cID > 0 ? \Concrete\Core\Page\Page::getByID($cID) : null;
            $resultNode->cID = $p && !$p->isError() ? ('/' . ltrim((string) $p->getCollectionPath(), '/')) : '';
        }
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Block\BlockController::importAdditionalData()
     */
    protected function importAdditionalData($b, $blockNode)
    {
        $cn = $this->app->make(Connection::class);
        // First import the btSurveyOptions rows, storing a mapping from the optionID in the XML and the new optionID generated by the database
        $optionIDMap = [];
        foreach (($blockNode->xpath('./data[@table="btSurveyOptions"]/record') ?: []) as $optionNode) {
            $originalOptionID = isset($optionNode->optionID) ? (int) (string) $optionNode->optionID : 0;
            $cn->insert('btSurveyOptions', [
                'bID' => $b->getBlockID(),
                'optionName' => isset($optionNode->optionName) ? (string) $optionNode->optionName : '',
                'displayOrder' => isset($optionNode->displayOrder) ? (int) (string) $optionNode->displayOrder : 0,
            ]);
            if ($originalOptionID > 0) {
                $optionIDMap[$originalOptionID] = $cn->lastInsertId();
            }
        }
        $em = $this->app->make(EntityManagerInterface::class);
        $userRepo = $em->getRepository(\Concrete\Core\Entity\User\User::class);
        // Next, let's import the btSurveyResults rows
        foreach (($blockNode->xpath('./data[@table="btSurveyResults"]/record') ?: []) as $resultNode) {
            $originalOptionID = isset($resultNode->optionID) ? (int) (string) $resultNode->optionID : 0;
            if ($originalOptionID < 1 || !isset($optionIDMap[$originalOptionID])) {
                continue;
            }
            $data = [
                'bID' => $b->getBlockID(),
                'optionID' => $optionIDMap[$originalOptionID],
                'ipAddress' => isset($resultNode->ipAddress) ? (string) $resultNode->ipAddress : '',
                'uID' => 0,
                'cID' => 0,
            ];
            $timestamp = isset($resultNode->timestamp) ? (string) $resultNode->timestamp : '';
            if ($timestamp !== '') {
                $data['timestamp'] = $timestamp;
            }
            $serializedUser = explode(':', isset($resultNode->uID) ? (string) $resultNode->uID : '', 2);
            if (!empty($serializedUser[1]) && $serializedUser[0] === 'user') {
                $user = $userRepo->findOneBy(['uName' => $serializedUser[1]]);
                if ($user) {
                    $data['uID'] = $user->getUserID();
                }
            }
            $serializedPage = isset($resultNode->cID) ? (string) $resultNode->cID : '';
            if ($serializedPage !== '' && $serializedPage[0] == '/') {
                if ($serializedPage === '/') {
                    $cID = Page::getHomePageID();
                    $page = $cID ? Page::getByID($cID) : null;
                } else {
                    $page = Page::getByPath($serializedPage);
                }
                if ($page && !$page->isError()) {
                    $data['cID'] = $page->getCollectionID();
                }
            }
            $cn->insert('btSurveyResults', $data);
        }
    }
}
