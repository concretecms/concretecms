<?php

namespace Concrete\Block\Survey;

use Concrete\Core\Block\BlockController;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Feature\Features;
use Concrete\Core\Feature\UsesFeatureInterface;
use Concrete\Core\User\User;
use Core;
use Database;
use Doctrine\DBAL\Types\Types;
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
                    $_REQUEST['optionID'],
                    $this->bID,
                    $duID,
                    $ip,
                    $this->cID,
                ];
                $q = 'INSERT INTO btSurveyResults (optionID, bID, uID, ipAddress, cID) VALUES (?, ?, ?, ?, ?)';
                $db->query($q, $v);
                setcookie('ccmPoll' . $this->bID . '-' . $this->cID, 'voted', time() + 1296000, DIR_REL . '/');
                $this->redirect($c->getCollectionPath() . '?survey_voted=1');
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
        if ($u->isRegistered()) {
            $db = Database::connection();
            $v = [$u->getUserID(), $this->bID, $this->cID];
            $q = 'SELECT count(resultID) AS total FROM btSurveyResults WHERE uID = ? AND bID = ? AND cID = ?';
            $result = $db->getOne($q, $v);
            if ($result > 0) {
                return true;
            }
        } elseif ($_COOKIE['ccmPoll' . $this->bID . '-' . $this->cID] == 'voted') {
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
}
