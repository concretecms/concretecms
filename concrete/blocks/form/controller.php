<?php
namespace Concrete\Block\Form;

use Concrete\Core\Block\BlockController;
use Concrete\Core\Entity\File\Version;
use Config;
use Core;
use Database;
use Events;
use Exception;
use File;
use FileImporter;
use FileSet;
use Page;
use User;
use UserInfo;

class Controller extends BlockController
{
    public $btTable = 'btForm';
    public $btQuestionsTablename = 'btFormQuestions';
    public $btAnswerSetTablename = 'btFormAnswerSet';
    public $btAnswersTablename = 'btFormAnswers';
    public $btInterfaceWidth = '420';
    public $btInterfaceHeight = '430';
    public $thankyouMsg = '';
    public $submitText = '';
    public $noSubmitFormRedirect = 0;
    protected $btCacheBlockRecord = false;
    protected $btExportTables = ['btForm', 'btFormQuestions'];
    protected $btExportPageColumns = ['redirectCID'];
    protected $lastAnswerSetId = 0;
    protected $btCopyWhenPropagate = true;

    /**
     * Used for localization. If we want to localize the name/description we have to include this.
     *
     * @return string
     */
    public function getBlockTypeDescription()
    {
        return t('Build simple forms and surveys.');
    }

    public function getBlockTypeName()
    {
        return t('Legacy Form');
    }

    public function getJavaScriptStrings()
    {
        return [
            'delete-question' => t('Are you sure you want to delete this question?'),
            'form-name' => t('Your form must have a name.'),
            'complete-required' => t('Please complete all required fields.'),
            'ajax-error' => t('AJAX Error.'),
            'form-min-1' => t('Please add at least one question to your form.'),
        ];
    }

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
                            if ($table == 'btFormQuestions') {
                                $db = Database::connection();
                                $aar->questionSetId = $db->GetOne('select questionSetId from btForm where bID = ?', [$b->getBlockID()]);
                            }
                            $aar->Replace();
                        }
                    }
                }
            }
        }
    }

    public function __construct($b = null)
    {
        parent::__construct($b);
        //$this->bID = intval($this->_bID);
        if (is_string($this->thankyouMsg) && !strlen($this->thankyouMsg)) {
            $this->thankyouMsg = $this->getDefaultThankYouMsg();
        }
        if (is_string($this->submitText) && !strlen($this->submitText)) {
            $this->submitText = $this->getDefaultSubmitText();
        }
    }

    /**
     * Internal helper function.
     */
    private function viewRequiresJqueryUI()
    {
        $whereInputTypes = "inputType = 'date' OR inputType = 'datetime'";
        $sql = "SELECT COUNT(*) FROM {$this->btQuestionsTablename} WHERE questionSetID = ? AND bID = ? AND ({$whereInputTypes})";
        $vals = [intval($this->questionSetId), intval($this->bID)];
        $JQUIFieldCount = Database::connection()->GetOne($sql, $vals);

        return (bool) $JQUIFieldCount;
    }

    // we are not using registerViewAssets because this block doesn't support caching
    // and we have some block record things we need to check.
    public function view()
    {
        if ($this->viewRequiresJqueryUI()) {
            $this->requireAsset('jquery/ui');
        }
        $this->requireAsset('css', 'core/frontend/errors');
        if ($this->displayCaptcha) {
            $this->requireAsset('css', 'core/frontend/captcha');
        }
    }

    public function getDefaultThankYouMsg()
    {
        return t('Thanks!');
    }

    public function getDefaultSubmitText()
    {
        return 'Submit';
    }

    /**
     * Form add or edit submit
     * (run after the duplicate method on first block edit of new page version).
     */
    public function save($data = [])
    {
        if (!$data || count($data) == 0) {
            $data = $_POST;
        }
        $data += [
            'qsID' => null,
            'oldQsID' => null,
            'questions' => [],
        ];

        $b = $this->getBlockObject();
        $c = $b->getBlockCollectionObject();

        $db = Database::connection();
        if (intval($this->bID) > 0) {
            $q = "select count(*) as total from {$this->btTable} where bID = " . intval($this->bID);
            $total = $db->getOne($q);
        } else {
            $total = 0;
        }

        if (isset($_POST['qsID']) && $_POST['qsID']) {
            $data['qsID'] = $_POST['qsID'];
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
        $data['bID'] = intval($this->bID);

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

        $v = [$data['qsID'], $data['surveyName'], $data['submitText'], intval($data['notifyMeOnSubmission']), $data['recipientEmail'], $data['thankyouMsg'], intval($data['displayCaptcha']), intval($data['redirectCID']), intval($data['addFilesToSet']), intval($this->bID)];

        //is it new?
        if (intval($total) == 0) {
            $q = "insert into {$this->btTable} (questionSetId, surveyName, submitText, notifyMeOnSubmission, recipientEmail, thankyouMsg, displayCaptcha, redirectCID, addFilesToSet, bID) values (?, ?, ?, ?, ?, ?, ?, ?, ?,?)";
        } else {
            $v[] = $data['qsID'];
            $q = "update {$this->btTable} set questionSetId = ?, surveyName=?, submitText=?, notifyMeOnSubmission=?, recipientEmail=?, thankyouMsg=?, displayCaptcha=?, redirectCID=?, addFilesToSet=? where bID = ? AND questionSetId= ?";
        }

        $rs = $db->query($q, $v);

        //Add Questions (for programmatically creating forms, such as during the site install)
        if (count($data['questions']) > 0) {
            $miniSurvey = new MiniSurvey();
            foreach ($data['questions'] as $questionData) {
                $miniSurvey->addEditQuestion($questionData, 0);
            }
        }

        $this->questionVersioning($data);

        return true;
    }

    /**
     * Ties the new or edited questions to the new block number.
     * New and edited questions are temporarily given bID=0, until the block is saved... painfully complicated.
     *
     * @param array $data
     */
    protected function questionVersioning($data = [])
    {
        $data += [
            'ignoreQuestionIDs' => '',
            'pendingDeleteIDs' => '',
        ];
        $db = Database::connection();
        $oldBID = intval($data['bID']);

        //if this block is being edited a second time, remove edited questions with the current bID that are pending replacement
        //if( intval($oldBID) == intval($this->bID) ){
            $vals = [intval($data['oldQsID'])];
        $pendingQuestions = $db->getAll('SELECT msqID FROM btFormQuestions WHERE bID=0 && questionSetId=?', $vals);
        foreach ($pendingQuestions as $pendingQuestion) {
            $vals = [intval($this->bID), intval($pendingQuestion['msqID'])];
            $db->query('DELETE FROM btFormQuestions WHERE bID=? AND msqID=?', $vals);
        }
        //}

        //assign any new questions the new block id
        $vals = [intval($data['bID']), intval($data['qsID']), intval($data['oldQsID'])];
        $rs = $db->query('UPDATE btFormQuestions SET bID=?, questionSetId=? WHERE bID=0 && questionSetId=?', $vals);

        //These are deleted or edited questions.  (edited questions have already been created with the new bID).
        $ignoreQuestionIDsDirty = explode(',', $data['ignoreQuestionIDs']);
        $ignoreQuestionIDs = [0];
        foreach ($ignoreQuestionIDsDirty as $msqID) {
            $ignoreQuestionIDs[] = intval($msqID);
        }
        $ignoreQuestionIDstr = implode(',', $ignoreQuestionIDs);

        //remove any questions that are pending deletion, that already have this current bID
        $pendingDeleteQIDsDirty = explode(',', $data['pendingDeleteIDs']);
        $pendingDeleteQIDs = [];
        foreach ($pendingDeleteQIDsDirty as $msqID) {
            $pendingDeleteQIDs[] = intval($msqID);
        }
        $vals = [$this->bID, intval($data['qsID'])];
        $pendingDeleteQIDs = implode(',', $pendingDeleteQIDs);
        $unchangedQuestions = $db->query('DELETE FROM btFormQuestions WHERE bID=? AND questionSetId=? AND msqID IN (' . $pendingDeleteQIDs . ')', $vals);
    }

    /**
     * Duplicate will run when copying a page with a block, or editing a block for the first time within a page version (before the save).
     */
    public function duplicate($newBID)
    {
        $b = $this->getBlockObject();
        $c = $b->getBlockCollectionObject();

        $db = Database::connection();
        $v = [$this->bID];
        $q = "select * from {$this->btTable} where bID = ? LIMIT 1";
        $r = $db->query($q, $v);
        $row = $r->fetchRow();

        //if the same block exists in multiple collections with the same questionSetID
        if (count($row) > 0) {
            $oldQuestionSetId = $row['questionSetId'];

            //It should only generate a new question set id if the block is copied to a new page,
            //otherwise it will loose all of its answer sets (from all the people who've used the form on this page)
            $questionSetCIDs = $db->getCol("SELECT distinct cID FROM {$this->btTable} AS f, CollectionVersionBlocks AS cvb " .
                        'WHERE f.bID=cvb.bID AND questionSetId=' . intval($row['questionSetId']));

            //this question set id is used on other pages, so make a new one for this page block
            if (count($questionSetCIDs) > 1 || !in_array($c->cID, $questionSetCIDs)) {
                $newQuestionSetId = time();
                $_POST['qsID'] = $newQuestionSetId;
            } else {
                //otherwise the question set id stays the same
                $newQuestionSetId = $row['questionSetId'];
            }

            //duplicate survey block record
            //with a new Block ID and a new Question
            $v = [$newQuestionSetId, $row['surveyName'], $row['submitText'], $newBID, $row['thankyouMsg'], intval($row['notifyMeOnSubmission']), $row['recipientEmail'], $row['displayCaptcha'], $row['addFilesToSet']];
            $q = "insert into {$this->btTable} ( questionSetId, surveyName, submitText, bID,thankyouMsg,notifyMeOnSubmission,recipientEmail,displayCaptcha,addFilesToSet) values (?, ?, ?, ?, ?, ?, ?, ?,?)";
            $result = $db->Execute($q, $v);

            $rs = $db->query("SELECT * FROM {$this->btQuestionsTablename} WHERE questionSetId=$oldQuestionSetId AND bID=" . intval($this->bID));
            while ($row = $rs->fetchRow()) {
                $v = [$newQuestionSetId, intval($row['msqID']), intval($newBID), $row['question'], $row['inputType'], $row['options'], $row['position'], $row['width'], $row['height'], $row['required'], $row['defaultDate']];
                $sql = "INSERT INTO {$this->btQuestionsTablename} (questionSetId,msqID,bID,question,inputType,options,position,width,height,required,defaultDate) VALUES (?,?,?,?,?,?,?,?,?,?,?)";
                $db->Execute($sql, $v);
            }

            return $newQuestionSetId;
        }

        return 0;
    }

    /**
     * Users submits the completed survey.
     *
     * @param int $bID
     */
    public function action_submit_form($bID = false)
    {
        if ($this->bID != $bID) {
            return false;
        }

        $ip = Core::make('helper/validation/ip');
        $this->view();

        if ($ip->isBlacklisted()) {
            $this->set('invalidIP', $ip->getErrorMessage());

            return;
        }

        $txt = Core::make('helper/text');
        $db = Database::connection();

        //question set id
        $qsID = intval($_POST['qsID']);
        if ($qsID == 0) {
            throw new Exception(t("Oops, something is wrong with the form you posted (it doesn't have a question set id)."));
        }

        $token = Core::make('token');
        if (!$token->validate('form_block_submit_qs_' . $qsID)) {
            throw new Exception(t('Invalid Request'));
        }

        //get all questions for this question set
        $rows = $db->GetArray("SELECT * FROM {$this->btQuestionsTablename} WHERE questionSetId=? AND bID=? order by position asc, msqID", [$qsID, intval($this->bID)]);

        if (!count($rows)) {
            throw new Exception(t("Oops, something is wrong with the form you posted (it doesn't have any questions)."));
        }

        $errorDetails = [];

        // check captcha if activated
        if ($this->displayCaptcha) {
            $captcha = Core::make('helper/validation/captcha');
            if (!$captcha->check()) {
                $errors['captcha'] = t('Incorrect captcha code');
                $_REQUEST['ccmCaptchaCode'] = '';
            }
        }

        //checked required fields
        foreach ($rows as $row) {
            if ($row['inputType'] == 'datetime') {
                if (!isset($datetime)) {
                    $datetime = Core::make('helper/form/date_time');
                }
                $translated = $datetime->translate('Question' . $row['msqID']);
                if ($translated) {
                    $_POST['Question' . $row['msqID']] = $translated;
                }
            }
            if (intval($row['required']) == 1) {
                $notCompleted = 0;
                if ($row['inputType'] == 'email') {
                    if (!Core::make('helper/validation/strings')->email($_POST['Question' . $row['msqID']])) {
                        $errors['emails'] = t('You must enter a valid email address.');
                        $errorDetails[$row['msqID']]['emails'] = $errors['emails'];
                    }
                }
                if ($row['inputType'] == 'checkboxlist') {
                    $answerFound = 0;
                    foreach ($_POST as $key => $val) {
                        if (strstr($key, 'Question' . $row['msqID'] . '_') && strlen($val)) {
                            $answerFound = 1;
                        }
                    }
                    if (!$answerFound) {
                        $notCompleted = 1;
                    }
                } elseif ($row['inputType'] == 'fileupload') {
                    if (!isset($_FILES['Question' . $row['msqID']]) || !is_uploaded_file($_FILES['Question' . $row['msqID']]['tmp_name'])) {
                        $notCompleted = 1;
                    }
                } elseif (!strlen(trim($_POST['Question' . $row['msqID']]))) {
                    $notCompleted = 1;
                }
                if ($notCompleted) {
                    $errors['CompleteRequired'] = t('Complete required fields *');
                    $errorDetails[$row['msqID']]['CompleteRequired'] = $errors['CompleteRequired'];
                }
            }
        }

        //try importing the file if everything else went ok
        $tmpFileIds = [];
        if (!count($errors)) {
            foreach ($rows as $row) {
                if ($row['inputType'] != 'fileupload') {
                    continue;
                }
                $questionName = 'Question' . $row['msqID'];
                if (!intval($row['required']) &&
                    (
                    !isset($_FILES[$questionName]['tmp_name']) || !is_uploaded_file($_FILES[$questionName]['tmp_name'])
                    )
                ) {
                    continue;
                }
                $fi = new FileImporter();
                $resp = $fi->import($_FILES[$questionName]['tmp_name'], $_FILES[$questionName]['name']);
                if (!($resp instanceof Version)) {
                    switch ($resp) {
                    case FileImporter::E_FILE_INVALID_EXTENSION:
                        $errors['fileupload'] = t('Invalid file extension.');
                        $errorDetails[$row['msqID']]['fileupload'] = $errors['fileupload'];
                        break;
                    case FileImporter::E_FILE_INVALID:
                        $errors['fileupload'] = t('Invalid file.');
                        $errorDetails[$row['msqID']]['fileupload'] = $errors['fileupload'];
                        break;
                }
                } else {
                    $tmpFileIds[intval($row['msqID'])] = $resp->getFileID();
                    if (intval($this->addFilesToSet)) {
                        $fs = new FileSet();
                        $fs = $fs->getByID($this->addFilesToSet);
                        if ($fs->getFileSetID()) {
                            $fs->addFileToSet($resp);
                        }
                    }
                }
            }
        }

        if (count($errors)) {
            $this->set('formResponse', t('Please correct the following errors:'));
            $this->set('errors', $errors);
            $this->set('errorDetails', $errorDetails);
        } else { //no form errors
            //save main survey record
            $u = new User();
            $uID = 0;
            if ($u->isRegistered()) {
                $uID = $u->getUserID();
            }
            $q = "insert into {$this->btAnswerSetTablename} (questionSetId, uID) values (?,?)";
            $db->query($q, [$qsID, $uID]);
            $answerSetID = $db->Insert_ID();
            $this->lastAnswerSetId = $answerSetID;

            $questionAnswerPairs = [];

            if (Config::get('concrete.email.form_block.address') && strstr(Config::get('concrete.email.form_block.address'), '@')) {
                $formFormEmailAddress = Config::get('concrete.email.form_block.address');
            } else {
                $adminUserInfo = UserInfo::getByID(USER_SUPER_ID);
                $formFormEmailAddress = $adminUserInfo->getUserEmail();
            }
            $replyToEmailAddress = $formFormEmailAddress;
            //loop through each question and get the answers
            foreach ($rows as $row) {
                //save each answer
                $answerDisplay = '';
                if ($row['inputType'] == 'checkboxlist') {
                    $answer = [];
                    $answerLong = '';
                    $keys = array_keys($_POST);
                    foreach ($keys as $key) {
                        if (strpos($key, 'Question' . $row['msqID'] . '_') === 0) {
                            $answer[] = $txt->sanitize($_POST[$key]);
                        }
                    }
                } elseif ($row['inputType'] == 'text') {
                    $answerLong = $txt->sanitize($_POST['Question' . $row['msqID']]);
                    $answer = '';
                } elseif ($row['inputType'] == 'fileupload') {
                    $answerLong = '';
                    $answer = intval($tmpFileIds[intval($row['msqID'])]);
                    if ($answer > 0) {
                        $answerDisplay = File::getByID($answer)->getVersion()->getDownloadURL();
                    } else {
                        $answerDisplay = t('No file specified');
                    }
                } elseif ($row['inputType'] == 'url') {
                    $answerLong = '';
                    $answer = $txt->sanitize($_POST['Question' . $row['msqID']]);
                } elseif ($row['inputType'] == 'email') {
                    $answerLong = '';
                    $answer = $txt->sanitize($_POST['Question' . $row['msqID']]);
                    if (!empty($row['options'])) {
                        $settings = unserialize($row['options']);
                        if (is_array($settings) && array_key_exists('send_notification_from', $settings) && $settings['send_notification_from'] == 1) {
                            $email = $txt->email($answer);
                            if (!empty($email)) {
                                $replyToEmailAddress = $email;
                            }
                        }
                    }
                } elseif ($row['inputType'] == 'telephone') {
                    $answerLong = '';
                    $answer = $txt->sanitize($_POST['Question' . $row['msqID']]);
                } else {
                    $answerLong = '';
                    $answer = $txt->sanitize($_POST['Question' . $row['msqID']]);
                }

                if (is_array($answer)) {
                    $answer = implode(',', $answer);
                }

                $questionAnswerPairs[$row['msqID']]['question'] = $row['question'];
                $questionAnswerPairs[$row['msqID']]['answer'] = $txt->sanitize($answer . $answerLong);
                $questionAnswerPairs[$row['msqID']]['answerDisplay'] = strlen($answerDisplay) ? $answerDisplay : $questionAnswerPairs[$row['msqID']]['answer'];

                $v = [$row['msqID'], $answerSetID, $answer, $answerLong];
                $q = "insert into {$this->btAnswersTablename} (msqID,asID,answer,answerLong) values (?,?,?,?)";
                $db->query($q, $v);
            }
            $foundSpam = false;

            $submittedData = '';
            foreach ($questionAnswerPairs as $questionAnswerPair) {
                $submittedData .= $questionAnswerPair['question'] . "\r\n" . $questionAnswerPair['answer'] . "\r\n" . "\r\n";
            }
            $antispam = Core::make('helper/validation/antispam');
            if (!$antispam->check($submittedData, 'form_block')) {
                // found to be spam. We remove it
                $foundSpam = true;
                $q = "delete from {$this->btAnswerSetTablename} where asID = ?";
                $v = [$this->lastAnswerSetId];
                $db->Execute($q, $v);
                $db->Execute("delete from {$this->btAnswersTablename} where asID = ?", [$this->lastAnswerSetId]);
            }

            if (intval($this->notifyMeOnSubmission) > 0 && !$foundSpam) {
                if (Config::get('concrete.email.form_block.address') && strstr(Config::get('concrete.email.form_block.address'), '@')) {
                    $formFormEmailAddress = Config::get('concrete.email.form_block.address');
                } else {
                    $adminUserInfo = UserInfo::getByID(USER_SUPER_ID);
                    $formFormEmailAddress = $adminUserInfo->getUserEmail();
                }

                $mh = Core::make('helper/mail');
                $mh->to($this->recipientEmail);
                $mh->from($formFormEmailAddress);
                $mh->replyto($replyToEmailAddress);
                $mh->addParameter('formName', $this->surveyName);
                $mh->addParameter('questionSetId', $this->questionSetId);
                $mh->addParameter('questionAnswerPairs', $questionAnswerPairs);
                $mh->load('block_form_submission');
                $mh->setSubject(t('%s Form Submission', $this->surveyName));
                //echo $mh->body.'<br>';
                @$mh->sendMail();
            }

            //launch form submission event with dispatch method
            $formEventData = [];
            $formEventData['bID'] = intval($this->bID);
            $formEventData['questionSetID'] = $this->questionSetId;
            $formEventData['replyToEmailAddress'] = $replyToEmailAddress;
            $formEventData['formFormEmailAddress'] = $formFormEmailAddress;
            $formEventData['questionAnswerPairs'] = $questionAnswerPairs;
            $event = new \Symfony\Component\EventDispatcher\GenericEvent();
            $event->setArgument('formData', $formEventData);
            Events::dispatch('on_form_submission', $event);

            if (!$this->noSubmitFormRedirect) {
                $targetPage = null;
                if ($this->redirectCID == HOME_CID) {
                    $targetPage = Page::getByID(HOME_CID);
                } elseif ($this->redirectCID > 0) {
                    $pg = Page::getByID($this->redirectCID);
                    if (is_object($pg) && $pg->cID) {
                        $targetPage = $pg;
                    }
                }
                if (is_object($targetPage)) {
                    $response = \Redirect::page($targetPage);
                } else {
                    $response = \Redirect::page(Page::getCurrentPage());
                    $url = $response->getTargetUrl() . '?surveySuccess=1&qsid=' . $this->questionSetId . '#formblock' . $this->bID;
                    $response->setTargetUrl($url);
                }
                $response->send();
                exit;
            }
        }
    }

    public function delete()
    {
        $db = Database::connection();

        $deleteData['questionsIDs'] = [];
        $deleteData['strandedAnswerSetIDs'] = [];

        $miniSurvey = new MiniSurvey();
        $info = $miniSurvey->getMiniSurveyBlockInfo($this->bID);

        //get all answer sets
        $q = "SELECT asID FROM {$this->btAnswerSetTablename} WHERE questionSetId = " . intval($info['questionSetId']);
        $answerSetsRS = $db->query($q);

        //delete the questions
        $deleteData['questionsIDs'] = $db->getAll("SELECT qID FROM {$this->btQuestionsTablename} WHERE questionSetId = " . intval($info['questionSetId']) . ' AND bID=' . intval($this->bID));
        foreach ($deleteData['questionsIDs'] as $questionData) {
            $db->query("DELETE FROM {$this->btQuestionsTablename} WHERE qID=" . intval($questionData['qID']));
        }

        //delete left over answers
        $strandedAnswerIDs = $db->getAll('SELECT fa.aID FROM `btFormAnswers` AS fa LEFT JOIN btFormQuestions as fq ON fq.msqID=fa.msqID WHERE fq.msqID IS NULL');
        foreach ($strandedAnswerIDs as $strandedAnswer) {
            $db->query('DELETE FROM `btFormAnswers` WHERE aID=' . intval($strandedAnswer['aID']));
        }

        //delete the left over answer sets
        $deleteData['strandedAnswerSetIDs'] = $db->getAll('SELECT aset.asID FROM btFormAnswerSet AS aset LEFT JOIN btFormAnswers AS fa ON aset.asID=fa.asID WHERE fa.asID IS NULL');
        foreach ($deleteData['strandedAnswerSetIDs'] as $strandedAnswerSetIDs) {
            $db->query('DELETE FROM btFormAnswerSet WHERE asID=' . intval($strandedAnswerSetIDs['asID']));
        }

        //delete the form block
        $q = "delete from {$this->btTable} where bID = '{$this->bID}'";
        $r = $db->query($q);

        parent::delete();

        return $deleteData;
    }
}
