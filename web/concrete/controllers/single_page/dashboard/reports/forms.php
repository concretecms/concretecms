<?php

namespace Concrete\Controller\SinglePage\Dashboard\Reports;

use Concrete\Core\File\File;
use \Concrete\Core\Page\Controller\DashboardPageController;
use Loader,
    UserInfo,
    Page;
use \Concrete\Block\Form\MiniSurvey;
use \Concrete\Block\Form\Statistics as FormBlockStatistics;

class Forms extends DashboardPageController
{

    protected $pageSize = 10;

    public function view()
    {
        if ($_REQUEST['all']) {
            $this->pageSize = 100000;
            $_REQUEST['page'] = 1;
        }
        $this->loadSurveyResponses();
    }

    public function csv()
    {
        $dateHelper = Loader::helper('date');
        /* @var $dateHelper \Concrete\Core\Localization\Service\Date */

        $this->pageSize = 0;
        $this->loadSurveyResponses();
        $textHelper = Loader::helper('text');

        $questionSet = $this->get('questionSet');
        $answerSets = $this->get('answerSets');
        $questions = $this->get('questions');
        $surveys = $this->get('surveys');

        $escapeCharacter = "'";
        $charactersToEscape = array('-', '+', '=');

        $fileName = $textHelper->filterNonAlphaNum($surveys[$questionSet]['surveyName']);

        header("Content-Type: text/csv");
        header("Cache-control: private");
        header("Pragma: public");
        $date = date('Ymd');
        header("Content-Disposition: attachment; filename=" . $fileName . "_form_data_{$date}.csv");

        $fp = fopen('php://output', 'w');

        // write the columns
        $row = array(
            t('Submitted Date'),
            t('User'),
        );

        foreach ($questions as $questionId => $question) {
            if ($question['inputType'] == 'checkboxlist') {
                $options = explode('%%', $question['options']);
                foreach ($options as $opt) {
                    $row[] = $questions[$questionId]['question'] . ': ' . $opt;
                }
            } else {
                $row[] = $questions[$questionId]['question'];
            }
        }

        fputcsv($fp, $row);

        // write the data
        foreach ($answerSets as $answerSet) {
            $row = array();
            $row[] = $dateHelper->formatCustom($dateHelper::DB_FORMAT, $answerSet['created']);

            if ($answerSet['uID'] > 0) {
                $ui = UserInfo::getByID($answerSet['uID']);
                if (is_object($ui)) {
                    $row[] = $ui->getUserName();
                }
            } else {
                $row[] = '';
            }

            foreach ($questions as $questionId => $question) {
                if ($question['inputType'] == 'checkboxlist') {
                    $options = explode('%%', $question['options']);
                    $subanswers = explode(',', $answerSet['answers'][$questionId]['answer']);
                    for ($i = 1; $i <= count($options); $i++) {
                        if (in_array(trim($options[$i - 1]), $subanswers)) {
                            $row[] = 'x';
                        } else {
                            $row[] = '';
                        }
                    }
                } else {
                    if ($question['inputType'] == 'fileupload') {
                        $fID = intval($answerSet['answers'][$questionId]['answer']);
                        $file = File::getByID($fID);
                        if ($fID && $file) {
                            $fileVersion = $file->getApprovedVersion();
                            $row[] = $fileVersion->getDownloadURL();
                        } else {
                            $row[] = t('File not found');
                        }
                    } else {
                        $answer = $answerSet['answers'][$questionId]['answer'] . $answerSet['answers'][$questionId]['answerLong'];

                        if (in_array(substr($answer, 0, 1), $charactersToEscape)) {
                            $row[] = $escapeCharacter . $answer;
                        } else {
                            $row[] = $answer;
                        }
                    }
                }
            }

            fputcsv($fp, $row);
        }

        fclose($fp);
        die;
    }

    private function loadSurveyResponses()
    {
        $c = Page::getCurrentPage();
        $db = Loader::db();
        $tempMiniSurvey = new MiniSurvey();
        $pageBase = \URL::to($c);

        if ($_REQUEST['action'] == 'deleteForm') {
            if (!Loader::helper('validation/token')->validate('deleteForm')) {
                $this->error->add(t('Invalid Token.'));
            } else {
                $this->deleteForm($_REQUEST['bID'], $_REQUEST['qsID']);
            }
        }

        if ($_REQUEST['action'] == 'deleteFormAnswers') {
            if (!Loader::helper('validation/token')->validate('deleteFormAnswers')) {
                $this->error->add(t('Invalid Token.'));
            } else {
                $this->deleteFormAnswers($_REQUEST['qsID']);
                $this->redirect('/dashboard/reports/forms');
            }
        }

        if ($_REQUEST['action'] == 'deleteResponse') {
            if (!Loader::helper('validation/token')->validate('deleteResponse')) {
                $this->error->add(t('Invalid Token.'));
            } else {
                $this->deleteAnswers($_REQUEST['asid']);
            }
        }

        //load surveys
        $surveysRS = FormBlockStatistics::loadSurveys($tempMiniSurvey);

        //index surveys by question set id
        $surveys = array();
        while ($survey = $surveysRS->fetchRow()) {
            //get Survey Answers
            $survey['answerSetCount'] = MiniSurvey::getAnswerCount($survey['questionSetId']);
            $surveys[$survey['questionSetId']] = $survey;
        }


        //load requested survey response
        if (!empty($_REQUEST['qsid'])) {
            $questionSet = intval(preg_replace('/[^[:alnum:]]/', '', $_REQUEST['qsid']));

            //get Survey Questions
            $questionsRS = MiniSurvey::loadQuestions($questionSet);
            $questions = array();
            while ($question = $questionsRS->fetchRow()) {
                $questions[$question['msqID']] = $question;
            }

            //get Survey Answers
            $answerSetCount = MiniSurvey::getAnswerCount($questionSet);

            //pagination 
            $pageBaseSurvey = $pageBase . '?qsid=' . $questionSet;
            $paginator = Loader::helper('pagination');
            $sortBy = $_REQUEST['sortBy'];
            $paginator->init(
                (int)$_REQUEST['page'], $answerSetCount, $pageBaseSurvey . '&page=%pageNum%&sortBy=' . $sortBy,
                $this->pageSize
            );

            if ($this->pageSize > 0) {
                $limit = $paginator->getLIMIT();
            } else {
                $limit = '';
            }
            $answerSets = FormBlockStatistics::buildAnswerSetsArray($questionSet, $sortBy, $limit);
        }
        $this->set('questions', $questions);
        $this->set('answerSets', $answerSets);
        $this->set('paginator', $paginator);
        $this->set('questionSet', $questionSet);
        $this->set('surveys', $surveys);
    }

    // SET UP DELETE FUNCTIONS HERE
    // DELETE SUBMISSIONS
    private function deleteAnswers($asID)
    {
        $db = Loader::db();
        $v = array(intval($asID));
        $q = 'DELETE FROM btFormAnswers WHERE asID = ?';
        $r = $db->query($q, $v);

        $q = 'DELETE FROM btFormAnswerSet WHERE asID = ?';
        $r = $db->query($q, $v);
    }

    //DELETE A FORM ANSWERS
    private function deleteFormAnswers($qsID)
    {
        $db = Loader::db();
        $v = array(intval($qsID));
        $q = 'SELECT asID FROM btFormAnswerSet WHERE questionSetId = ?';

        $r = $db->query($q, $v);
        while ($row = $r->fetchRow()) {
            $asID = $row['asID'];
            $this->deleteAnswers($asID);
        }
    }

    //DELETE FORMS AND ALL SUBMISSIONS
    private function deleteForm($bID, $qsID)
    {
        $db = Loader::db();
        $this->deleteFormAnswers($qsID);

        $v = array(intval($bID));
        $q = 'DELETE FROM btFormQuestions WHERE bID = ?';
        $r = $db->query($q, $v);

        $q = 'DELETE FROM btForm WHERE bID = ?';
        $r = $db->query($q, $v);

        $q = 'DELETE FROM Blocks WHERE bID = ?';
        $r = $db->query($q, $v);
    }

}
