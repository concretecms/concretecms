<?php

namespace Concrete\Block\Form;

use Concrete\Core\Block\BlockController;
use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Events\EventDispatcher;
use Concrete\Core\File\Import\ImportException;
use Concrete\Core\File\Set\Set as FileSet;
use Concrete\Core\Http\ResponseFactoryInterface;
use Concrete\Core\Page\Page;
use Concrete\Core\User\User;
use Concrete\Core\User\UserInfo;
use Concrete\Core\Validator\String\EmailValidator;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class Controller extends BlockController
{
    /**
     * @var string
     */
    public $btTable = 'btForm';

    /**
     * @var string
     */
    public $btQuestionsTablename = 'btFormQuestions';

    /**
     * @var string
     */
    public $btAnswerSetTablename = 'btFormAnswerSet';

    /**
     * @var string
     */
    public $btAnswersTablename = 'btFormAnswers';

    /**
     * @var string
     */
    public $btInterfaceWidth = '525';

    /**
     * @var string
     */
    public $btInterfaceHeight = '550';

    /**
     * @var string
     */
    public $thankyouMsg = '';

    /**
     * @var string
     */
    public $submitText = '';

    /**
     * @var int
     */
    public $noSubmitFormRedirect = 0;

    /**
     * @var int|null
     */
    public $displayCaptcha;

    /**
     * @var int|null
     */
    public $addFilesToSet;

    /**
     * @var string|null
     */
    public $recipientEmail;

    /**
     * @var bool|null
     */
    public $notifyMeOnSubmission;

    /**
     * @var string|null
     */
    public $surveyName;

    /**
     * @var int|null
     */
    public $questionSetId;

    /**
     * @var int|null
     */
    public $redirectCID;

    /**
     * @var bool
     */
    protected $btCacheBlockRecord = false;

    /**
     * @var string[]
     */
    protected $btExportTables = ['btForm', 'btFormQuestions'];

    /**
     * @var string[]
     */
    protected $btExportPageColumns = ['redirectCID'];

    /**
     * @var int|string
     */
    protected $lastAnswerSetId = 0;

    /**
     * @var bool
     */
    protected $btCopyWhenPropagate = true;

    /**
     * @param \Concrete\Core\Entity\Block\BlockType\BlockType|null $obj
     */
    public function __construct($obj = null)
    {
        parent::__construct($obj);
        //$this->bID = intval($this->_bID);
        if ($this->thankyouMsg === '') {
            $this->thankyouMsg = $this->getDefaultThankYouMsg();
        }
        if ($this->submitText === '') {
            $this->submitText = $this->getDefaultSubmitText();
        }
    }

    /**
     * Used for localization. If we want to localize the name/description we have to include this.
     *
     * @return string
     */
    public function getBlockTypeDescription()
    {
        return t('Build simple forms and surveys.');
    }

    /**
     * @return string
     */
    public function getBlockTypeName()
    {
        return t('Legacy Form');
    }

    // we are not using registerViewAssets because this block doesn't support caching
    // and we have some block record things we need to check.

    /**
     * @return void
     */
    public function view()
    {
        $this->requireAsset('css', 'core/frontend/errors');
        if ($this->displayCaptcha) {
            $this->requireAsset('css', 'core/frontend/captcha');
        }
    }

    /**
     * @return string
     */
    public function getDefaultThankYouMsg()
    {
        return t('Thanks!');
    }

    /**
     * @return string
     */
    public function getDefaultSubmitText()
    {
        return 'Submit';
    }

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     *
     * @return void
     */
    public function add()
    {
        $this->formSetup();
    }

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     *
     * @return void
     */
    public function edit()
    {
        $this->formSetup();
    }

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     *
     * @return void
     */
    public function formSetup()
    {
        $uih = $this->app->make('helper/concrete/ui');
        $uh = $this->app->make('helper/concrete/urls');
        $form = $this->app->make('helper/form');
        $datetime = $this->app->make('helper/form/date_time');
        $page_selector = $this->app->make('helper/form/page_selector');
        $bt = BlockType::getByHandle('form');
        $a = $this->getAreaObject();

        $this->set('uih', $uih);
        $this->set('uh', $uh);
        $this->set('form', $form);
        $this->set('datetime', $datetime);
        $this->set('ih', $uih);
        $this->set('page_selector', $page_selector);
        $this->set('bt', $bt);
        $this->set('a', $a);
        $this->set('addSelected', true);
    }

    /**
     * Form add or edit submit
     * (run after the duplicate method on first block edit of new page version).
     *
     * @param array<string,mixed>|null $data
     *
     * @throws \Doctrine\DBAL\Exception
     * @throws \Doctrine\DBAL\Driver\Exception|\Illuminate\Contracts\Container\BindingResolutionException
     *
     * @return void
     */
    public function save($data = [])
    {
        if (!is_array($data) || count($data) === 0) {
            $data = $this->request->request->all();
        }
        $data += [
            'qsID' => null,
            'oldQsID' => null,
            'questions' => [],
        ];
        /** @var Connection $db */
        $db = $this->app->make(Connection::class);
        if ((int) ($this->bID) > 0) {
            $q = $db->executeQuery("select count(bID) as total from {$this->btTable} where bID = " . (int) ($this->bID))->fetchOne();

            $total = $q;
        } else {
            $total = 0;
        }

        if ($this->request->request->has('qsID') && $this->request->request->get('qsID')) {
            $data['qsID'] = $this->request->request->get('qsID');
        }
        if (!$data['qsID']) {
            $data['qsID'] = time();
        }

        if (isset($data['questionSetId'])) {
            // This is specifically set when using the migration tool
            $data['qsID'] = $data['questionSetId'];
        }

        if (!$data['oldQsID']) {
            $data['oldQsID'] = $data['qsID'];
        }
        $data['bID'] = (int) ($this->bID);

        if (!empty($data['redirectCID'])) {
            $data['redirect'] = 1;
        } else {
            $data['redirect'] = 0;
            $data['redirectCID'] = 0;
        }

        if (empty($data['addFilesToSet'])) {
            $data['addFilesToSet'] = 0;
        }

        if (!isset($data['surveyName'])) {
            $data['surveyName'] = '';
        }

        if (!isset($data['submitText'])) {
            $data['submitText'] = '';
        }

        if (!isset($data['notifyMeOnSubmission'])) {
            $data['notifyMeOnSubmission'] = 0;
        }

        if (!isset($data['thankyouMsg'])) {
            $data['thankyouMsg'] = '';
        }

        if (!isset($data['displayCaptcha'])) {
            $data['displayCaptcha'] = 0;
        }

        $v = [
            $data['qsID'],
            $data['surveyName'],
            $data['submitText'],
            (int) ($data['notifyMeOnSubmission']),
            trim($data['recipientEmail'], ' ..,'),
            $data['thankyouMsg'],
            (int) ($data['displayCaptcha']),
            (int) ($data['redirectCID']),
            (int) ($data['addFilesToSet']),
            (int) ($this->bID),
        ];

        $types = [Types::INTEGER, Types::STRING, Types::STRING, Types::BOOLEAN, Types::STRING, Types::TEXT, Types::INTEGER, Types::INTEGER];
        if ((int) $total === 0) {
            $q = "insert into {$this->btTable} (questionSetId, surveyName, submitText, notifyMeOnSubmission, recipientEmail, thankyouMsg, displayCaptcha, redirectCID, addFilesToSet, bID) values (?, ?, ?, ?, ?, ?, ?, ?, ?,?)";
        } else {
            $v[] = $data['qsID'];
            $types[] = Types::INTEGER;
            $q = "update {$this->btTable} set questionSetId = ?, surveyName=?, submitText=?, notifyMeOnSubmission=?, recipientEmail=?, thankyouMsg=?, displayCaptcha=?, redirectCID=?, addFilesToSet=? where bID = ? AND questionSetId= ?";
        }

        $db->executeStatement($q, $v, $types);

        //Add Questions (for programmatically creating forms, such as during the site install)
        if (count($data['questions']) > 0) {
            $miniSurvey = new MiniSurvey();
            foreach ($data['questions'] as $questionData) {
                $miniSurvey->addEditQuestion($questionData, 0);
            }
        }

        $this->questionVersioning($data);
    }

    /**
     * Duplicate will run when copying a page with a block, or editing a block for the first time within a page version (before the save).
     *
     * @param string|int $newBID
     *
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Doctrine\DBAL\Exception|\Illuminate\Contracts\Container\BindingResolutionException
     *
     * @return \Concrete\Core\Legacy\BlockRecord|null
     */
    public function duplicate($newBID)
    {
        $b = $this->getBlockObject();
        $c = $b->getBlockCollectionObject();

        /** @var Connection $db */
        $db = $this->app->make(Connection::class);
        $v = [$this->bID];
        $q = "select * from {$this->btTable} where bID = ? LIMIT 1";
        $r = $db->executeQuery($q, $v);
        $row = $r->fetchAssociative();

        //if the same block exists in multiple collections with the same questionSetID
        if ($row !== false && count($row) > 0) {
            $oldQuestionSetId = $row['questionSetId'];

            //It should only generate a new question set id if the block is copied to a new page,
            //otherwise it will lose all of its answer sets (from all the people who've used the form on this page)
            $questionSetCIDs = $db->fetchAllAssociative("SELECT distinct cID FROM {$this->btTable} AS f, CollectionVersionBlocks AS cvb " .
                        'WHERE f.bID=cvb.bID AND questionSetId=' . (int) ($row['questionSetId']));

            //this question set id is used on other pages, so make a new one for this page block
            if (count($questionSetCIDs) > 1 || !in_array($c->getCollectionID(), $questionSetCIDs)) {
                $newQuestionSetId = time();
                $_POST['qsID'] = $newQuestionSetId;
            } else {
                //otherwise, the question set id stays the same
                $newQuestionSetId = $row['questionSetId'];
            }

            //duplicate survey block record
            //with a new Block ID and a new Question
            $v = [$newQuestionSetId, $row['surveyName'], $row['submitText'], $newBID, $row['thankyouMsg'], (int) ($row['notifyMeOnSubmission']), $row['recipientEmail'], $row['displayCaptcha'], $row['addFilesToSet']];
            $q = "insert into {$this->btTable} ( questionSetId, surveyName, submitText, bID,thankyouMsg,notifyMeOnSubmission,recipientEmail,displayCaptcha,addFilesToSet) values (?, ?, ?, ?, ?, ?, ?, ?,?)";
            $db->executeStatement($q, $v);

            $rs = $db->executeQuery("SELECT * FROM {$this->btQuestionsTablename} WHERE questionSetId={$oldQuestionSetId} AND bID=" . (int) ($this->bID));
            $rows = $rs->fetchAllAssociative();
            foreach ($rows as $row) {
                $v = [$newQuestionSetId, (int) ($row['msqID']), (int) $newBID, $row['question'], $row['inputType'], $row['options'], $row['position'], $row['width'], $row['height'], $row['required'], $row['defaultDate']];
                $sql = "INSERT INTO {$this->btQuestionsTablename} (questionSetId,msqID,bID,question,inputType,options,position,width,height,required,defaultDate) VALUES (?,?,?,?,?,?,?,?,?,?,?)";
                $db->executeStatement($sql, $v);
            }
        }

        return null;
    }

    /**
     * Users submits the completed survey.
     *
     * @param int|bool|string $bID
     *
     * @throws UserMessageException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \Doctrine\DBAL\Exception
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|void
     */
    public function action_submit_form($bID = false)
    {
        if ($this->bID != $bID) {
            return;
        }

        $ip = $this->app->make('failed_login');
        $this->view();

        if ($ip->isDenylisted()) {
            $this->set('invalidIP', $ip->getErrorMessage());

            return;
        }

        /** @var \Concrete\Core\Utility\Service\Text $txt */
        $txt = $this->app->make('helper/text');
        /** @var Connection $db */
        $db = $this->app->make(Connection::class);

        //question set id
        $qsID = (int) ($this->request->request->get('qsID'));
        if ($qsID === 0) {
            throw new UserMessageException(t("Oops, something is wrong with the form you posted (it doesn't have a question set id)."));
        }
        $errors = [];

        $token = $this->app->make('token');
        if (!$token->validate('form_block_submit_qs_' . $qsID)) {
            $errors[] = $token->getErrorMessage();
        }

        //get all questions for this question set
        $rows = $db->fetchAllAssociative("SELECT * FROM {$this->btQuestionsTablename} WHERE questionSetId=? AND bID=? order by position asc, msqID", [$qsID, (int) ($this->bID)]);

        if (!count($rows)) {
            throw new UserMessageException(t("Oops, something is wrong with the form you posted (it doesn't have any questions)."));
        }

        $errorDetails = [];

        // check captcha if activated
        if ($this->displayCaptcha) {
            $captcha = $this->app->make('helper/validation/captcha');
            if (!$captcha->check()) {
                $errors['captcha'] = t('Incorrect captcha code');
                $this->request->request->set('ccmCaptchaCode', '');
            }
        }
        $files = [];
        $questionAnswerPairs = [];
        $submittedData = '';
        //get answers and checked required fields
        foreach ($rows as $row) {
            $answer = '';
            $answerLong = '';
            $answerDisplay = '';
            $notCompleted = false;
            $isRequired = (int) ($row['required']) === 1;
            $field = $this->request->request->get('Question' . $row['msqID']);
            if ($row['inputType'] === 'datetime') {
                if (!isset($datetime)) {
                    $datetime = $this->app->make('helper/form/date_time');
                }
                $translated = $datetime->translate('Question' . $row['msqID']);
                if ($translated) {
                    $field = $translated;
                }
                $formPage = $this->getCollectionObject();
                $answer = $txt->sanitize($field);
                if (empty($answer) && $isRequired) {
                    $notCompleted = true;
                }
                if ($formPage) {
                    $site = $formPage->getSite();
                    $timezone = $site->getTimezone();
                    $date = $this->app->make('date');
                    $answerDisplay = $date->formatDateTime($answer, false, false, $timezone);
                } else {
                    $answerDisplay = $answer;
                }
            } elseif ($row['inputType'] === 'date') {
                $answer = $txt->sanitize($field);
                if (empty($answer) && $isRequired) {
                    $notCompleted = true;
                }
            } elseif ($row['inputType'] === 'checkboxlist') {
                $answer = [];
                $answerFound = 0;
                foreach ($this->request->request->all() as $key => $val) {
                    if (strpos($key, 'Question' . $row['msqID'] . '_') !== false) {
                        $val = $txt->sanitize($val);
                        $answer[] = $val;
                        if ($answerFound === 0 && !empty($val)) {
                            $answerFound = 1;
                        }
                    }
                }
                if ($isRequired && !$answerFound) {
                    $notCompleted = true;
                }
            } elseif ($row['inputType'] === 'email') {
                $answer = $txt->sanitize($field);
                if ($isRequired) {
                    if (empty($field)) {
                        $notCompleted = true;
                    } else {
                        if (!isset($emailValidator)) {
                            $emailValidator = $this->app->make(EmailValidator::class);
                        }
                        $e = $this->app->make('error');
                        if (!$emailValidator->isValid($field, $e)) {
                            $errors['emails'] = $e->toText();
                            $errorDetails[$row['msqID']]['emails'] = $errors['emails'];
                        }
                    }
                }

                if (!empty($row['options'])) {
                    // Don't allow classes
                    $settings = unserialize($row['options'], [false]);
                    if (is_array($settings) && array_key_exists('send_notification_from', $settings) && $settings['send_notification_from'] == 1) {
                        $email = $txt->email($answer);
                        if (!empty($email)) {
                            $replyToEmailAddress = $email;
                        }
                    }
                }
            } elseif ($row['inputType'] === 'fileupload') {
                /** @var UploadedFile|null $file */
                $file = $this->request->files->get('Question' . $row['msqID']);
                if ($file instanceof UploadedFile && $file->isValid()) {
                    $files[] = $row;
                } elseif ($isRequired) {
                    $notCompleted = true;
                }
            } elseif ($row['inputType'] === 'text') {
                $answerLong = $txt->sanitize(trim($field));
                if ($isRequired && empty($answerLong)) {
                    $notCompleted = true;
                }
            } else {
                $answer = $txt->sanitize(trim($field));
                if ($isRequired && empty($answer)) {
                    $notCompleted = true;
                }
            }
            if ($isRequired && $notCompleted) {
                $errors['CompleteRequired'] = t('Complete required fields *');
                $errorDetails[$row['msqID']]['CompleteRequired'] = $errors['CompleteRequired'];
            }

            if (is_array($answer)) {
                $answer = implode(',', $answer);
            }
            $sanitizedAnswer = $txt->sanitize($answer . $answerLong);
            // Save answer details for later
            $questionAnswerPairs[$row['msqID']]['question'] = $row['question'];
            $questionAnswerPairs[$row['msqID']]['answer'] = $sanitizedAnswer;
            $questionAnswerPairs[$row['msqID']]['answerDisplay'] = $answerDisplay !== '' ? $answerDisplay : $sanitizedAnswer;
            $questionAnswerPairs[$row['msqID']]['originalAnswer'] = $answer;
            $questionAnswerPairs[$row['msqID']]['answerLong'] = $answerLong;

            $submittedData .= $row['question'] . "\r\n" . $sanitizedAnswer . "\r\n" . "\r\n";
        }

        //try importing the file if everything else went ok
        if (!count($errors) && count($files)) {
            /** @var \Concrete\Core\File\Import\FileImporter $importer */
            $importer = $this->app->make(\Concrete\Core\File\Import\FileImporter::class);

            foreach ($files as $row) {
                $questionName = 'Question' . $row['msqID'];

                $file = $this->request->files->get($questionName);
                try {
                    $response = $importer->importUploadedFile($file);
                } catch (ImportException $x) {
                    throw new UserMessageException($x->getMessage());
                }
                    if ((int) ($this->addFilesToSet)) {
                        $fs = FileSet::getByID($this->addFilesToSet);
                        if ($fs->getFileSetID()) {
                            $fs->addFileToSet($response);
                        }
                    }

                $answer = $response->getFileID();
                if ($answer > 0) {
                    $answerDisplay = (string) $response->getDownloadURL();
                } else {
                    $answerDisplay = t('No file specified');
                }

                $questionAnswerPairs[$row['msqID']]['question'] = $row['question'];
                $questionAnswerPairs[$row['msqID']]['answer'] = $answer;
                $questionAnswerPairs[$row['msqID']]['orginalAnswer'] = $answer;
                $questionAnswerPairs[$row['msqID']]['answerLong'] = '';
                $questionAnswerPairs[$row['msqID']]['answerDisplay'] = $answerDisplay !== '' ? $answerDisplay : $answer;
            }
        }

        if (count($errors)) {
            $this->set('formResponse', t('Please correct the following errors:'));
            $this->set('errors', $errors);
            $this->set('errorDetails', $errorDetails);
        } else { //no form errors
            //save main survey record
            $u = $this->app->make(User::class);
            $uID = 0;
            if ($u->isRegistered()) {
                $uID = $u->getUserID();
            }
            $q = "insert into {$this->btAnswerSetTablename} (questionSetId, uID) values (?,?)";
            $db->executeStatement($q, [$qsID, $uID]);
            $answerSetID = $db->lastInsertId();
            $this->lastAnswerSetId = $answerSetID;

            $blockAddress = $this->app->make('config')->get('concrete.email.form_block.address');

            if ($blockAddress && strpos($blockAddress, '@') !== false) {
                $formFormEmailAddress = $blockAddress;
            } else {
                $adminUserInfo = UserInfo::getByID(USER_SUPER_ID);
                $formFormEmailAddress = $adminUserInfo->getUserEmail();
            }
            $replyToEmailAddress = $replyToEmailAddress ?? $formFormEmailAddress;
            $antispam = $this->app->make('helper/validation/antispam');
            $foundSpam = true;
            if ($antispam->check($submittedData, 'form_block')) {
                // not spam so we add to the database
                $foundSpam = false;
                foreach ($questionAnswerPairs as $msqID => $questionAnswerPair) {
                    $answer = $questionAnswerPair['originalAnswer'] ?: $questionAnswerPair['answer'];
                    $answerLong = $questionAnswerPair['answerLong'];
                    $db->insert($this->btAnswersTablename, ['msqID' => $msqID, 'asID' => $answerSetID, 'answer' => $answer, 'answerLong' => $answerLong], [Types::INTEGER, Types::INTEGER, Types::STRING, Types::TEXT]);
                }
            }

            if ((int) ($this->notifyMeOnSubmission) > 0 && !$foundSpam) {
                $mh = $this->app->make('helper/mail');
                $mh->to($this->recipientEmail);
                $mh->from($formFormEmailAddress);
                $mh->replyto($replyToEmailAddress);
                $mh->addParameter('formName', $this->surveyName);
                $mh->addParameter('questionSetId', $this->questionSetId);
                $mh->addParameter('questionAnswerPairs', $questionAnswerPairs);
                $mh->load('block_form_submission');
                if (empty($mh->getSubject())) {
                    $mh->setSubject(t('%s Form Submission', $this->surveyName));
                }
                //echo $mh->body.'<br>';
                @$mh->sendMail();
            }

            //launch form submission event with dispatch method
            $formEventData = [];
            $formEventData['bID'] = (int) ($this->bID);
            $formEventData['questionSetID'] = $this->questionSetId;
            $formEventData['replyToEmailAddress'] = $replyToEmailAddress;
            $formEventData['formFormEmailAddress'] = $formFormEmailAddress;
            $formEventData['questionAnswerPairs'] = $questionAnswerPairs;
            $event = new \Symfony\Component\EventDispatcher\GenericEvent();
            $event->setArgument('formData', $formEventData);

            $this->app->make(EventDispatcher::class)->dispatch('on_form_submission', $event);

            if (!$this->noSubmitFormRedirect) {
                $targetPage = null;
                if ($this->redirectCID > 0) {
                    $pg = Page::getByID($this->redirectCID);
                    if (is_object($pg) && $pg->getCollectionID()) {
                        $targetPage = $pg;
                    }
                }
                if (is_object($targetPage)) {
                    $response = $this->buildRedirect([$targetPage]);
                } else {
                    $response = $this->buildRedirect([Page::getCurrentPage()]);
                    $url = $response->getTargetUrl() . '?surveySuccess=1&qsid=' . $this->questionSetId . '#formblock' . $this->bID;
                    $response->setTargetUrl($url);
                }

                return $response;
            }
        }
    }

    /**
     * @throws UserMessageException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     *
     * @return mixed
     */
    public function action_services()
    {
        $token = $this->app->make('token');
        if (!$token->validate('ccm-bt-form-service')) {
            throw new UserMessageException($token->getErrorMessage());
        }
        $miniSurvey = new MiniSurvey();
        /** @var ResponseFactoryInterface $rf */
        $rf = $this->app->make(ResponseFactoryInterface::class);
        switch ($this->request->query->get('mode')) {
            case 'addQuestion':
                // lets return actual json objects...
                $json = $miniSurvey->addEditQuestion($this->request->request->all(), false);

                return $rf->json($json);
            case 'getQuestion':
                ob_start();
                try {
                    $miniSurvey->getQuestionInfo((int) $this->request->query->get('qsID'), (int) $this->request->query->get('qID'));

                    return $rf->create(ob_get_contents(), 200, ['Content-Type' => 'text/plain; charset=' . APP_CHARSET]);
                } finally {
                    ob_end_clean();
                }
            case 'delQuestion':
                $miniSurvey->deleteQuestion((int) $this->request->query->get('qsID'), (int) $this->request->query->get('msqID'));

                return $rf->json(['success' => true]);
            case 'reorderQuestions':
                $miniSurvey->reorderQuestions((int) $this->request->request->get('qsID'), $this->request->request->get('qIDs'));

                return $rf->json(['success' => true]);
            case 'refreshSurvey':
            default:
                $showEdit = (int) $this->request->request->get('showEdit', $this->request->query->get('showEdit')) === 1;
                ob_start();
                try {
                    $miniSurvey->loadSurvey((int) $this->request->query->get('qsID'), $showEdit, (int) $this->bID, explode(',', $this->request->query->get('hide')), 1, 1);

                    return $rf->create(ob_get_contents());
                } finally {
                    ob_end_clean();
                }
        }
    }

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \Doctrine\DBAL\Exception
     *
     * @return array<string|mixed>
     */
    public function delete()
    {
        /** @var Connection $db */
        $db = $this->app->make(Connection::class);
        $deleteData = [];

        $miniSurvey = new MiniSurvey();
        $info = $miniSurvey->getMiniSurveyBlockInfo($this->bID);

        // Check if our result has a questionSetId
        if (!empty($info)) {
            //delete the questions
            $deleteData['questionsIDs'] = $db->fetchAllAssociative("SELECT qID FROM {$this->btQuestionsTablename} WHERE questionSetId = " . (int) ($info['questionSetId']) . ' AND bID=' . (int) ($this->bID));
            foreach ($deleteData['questionsIDs'] as $questionData) {
                $db->executeStatement("DELETE FROM {$this->btQuestionsTablename} WHERE qID=" . (int) ($questionData['qID']));
            }
        } else {
            $deleteData['questionsIDs'] = [];
        }

        //delete left over answers
        $strandedAnswerIDs = $db->fetchAllAssociative('SELECT fa.aID FROM `btFormAnswers` AS fa LEFT JOIN btFormQuestions as fq ON fq.msqID=fa.msqID WHERE fq.msqID IS NULL');
        foreach ($strandedAnswerIDs as $strandedAnswer) {
            $db->executeStatement('DELETE FROM `btFormAnswers` WHERE aID=' . (int) ($strandedAnswer['aID']));
        }

        //delete the leftover answer sets
        $deleteData['strandedAnswerSetIDs'] = $db->fetchAllAssociative('SELECT aset.asID FROM btFormAnswerSet AS aset LEFT JOIN btFormAnswers AS fa ON aset.asID=fa.asID WHERE fa.asID IS NULL');
        foreach ($deleteData['strandedAnswerSetIDs'] as $strandedAnswerSetIDs) {
            $db->executeStatement('DELETE FROM btFormAnswerSet WHERE asID=' . (int) ($strandedAnswerSetIDs['asID']));
        }

        //delete the form block
        $db->executeStatement("delete from {$this->btTable} where bID = '{$this->bID}'");

        parent::delete();

        return $deleteData;
    }

    /**
     * @param \Concrete\Core\Block\Block $b
     * @param \SimpleXMLElement $blockNode
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \Doctrine\DBAL\Exception
     *
     * @return void
     */
    protected function importAdditionalData($b, $blockNode)
    {
        if (isset($blockNode->data)) {
            foreach ($blockNode->data as $data) {
                if ($data['table'] != $this->getBlockTypeDatabaseTable()) {
                    $table = (string) $data['table'];
                    if (isset($data->record)) {
                        foreach ($data->record as $record) {
                            $aar = new \Concrete\Core\Legacy\BlockRecord($table);
                            $aar->bID = $b->getBlockID();
                            foreach ($record->children() as $node) {
                                $nodeName = $node->getName();
                                $aar->{$nodeName} = (string) $node;
                            }
                            if ($table === 'btFormQuestions') {
                                /** @var Connection $db */
                                $db = $this->app->make(Connection::class);
                                /** @phpstan-ignore-next-line */
                                $aar->questionSetId = $db->fetchOne('select questionSetId from btForm where bID = ?', [$b->getBlockID()]);
                            }
                            $aar->Replace();
                        }
                    }
                }
            }
        }
    }

    /**
     * Ties the new or edited questions to the new block number.
     * New and edited questions are temporarily given bID=0, until the block is saved... painfully complicated.
     *
     * @param array<string|mixed> $data
     *
     * @throws \Doctrine\DBAL\Exception|\Illuminate\Contracts\Container\BindingResolutionException
     *
     * @return void
     */
    protected function questionVersioning($data = [])
    {
        $data += [
            'ignoreQuestionIDs' => '',
            'pendingDeleteIDs' => '',
        ];
        /** @var Connection $db */
        $db = $this->app->make(Connection::class);

        //if this block is being edited a second time, remove edited questions with the current bID that are pending replacement
        //if( intval($oldBID) == intval($this->bID) ){
        $vals = [(int) ($data['oldQsID'])];
        $pendingQuestions = $db->fetchFirstColumn('SELECT msqID FROM btFormQuestions WHERE bID=0 && questionSetId=?', $vals);
        foreach ($pendingQuestions as $pendingQuestion) {
            $db->delete('btFormQuestions', ['bID' => $this->bID, 'msqID' => $pendingQuestion], [Types::INTEGER, Types::INTEGER]);
        }
        //}

        //assign any new questions the new block id
        $vals = [(int) ($data['bID']), (int) ($data['qsID']), (int) ($data['oldQsID'])];
        $db->executeStatement('UPDATE btFormQuestions SET bID=?, questionSetId=? WHERE bID=0 && questionSetId=?', $vals);

        //remove any questions that are pending deletion, that already have this current bID
        $pendingDeleteQIDsDirty = explode(',', $data['pendingDeleteIDs']);
        $pendingDeleteQIDs = [];
        foreach ($pendingDeleteQIDsDirty as $msqID) {
            $pendingDeleteQIDs[] = (int) $msqID;
        }
        $vals = [$this->bID, (int) ($data['qsID'])];
        $pendingDeleteQIDs = implode(',', $pendingDeleteQIDs);
        $db->executeStatement('DELETE FROM btFormQuestions WHERE bID=? AND questionSetId=? AND msqID IN (' . $pendingDeleteQIDs . ')', $vals);
    }
}
