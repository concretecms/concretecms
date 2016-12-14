<?php
namespace Concrete\Controller\SinglePage\Dashboard\Reports\Forms;

use Concrete\Core\File\File;
use Concrete\Core\Page\Controller\DashboardPageController;
use Loader;
use UserInfo;
use Page;
use Concrete\Block\Form\MiniSurvey;
use Concrete\Block\Form\Statistics as FormBlockStatistics;

class Legacy extends DashboardPageController
{
    protected $pageSize = 10;

    public function view()
    {
        if ($this->request->get('all')) {
            $this->pageSize = 100000;
            $this->request->attributes->set('page', 1);
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
        $charactersToEscape = ['-', '+', '='];

        $fileName = $textHelper->filterNonAlphaNum($surveys[$questionSet]['surveyName']);

        header("Content-Type: text/csv");
        header("Cache-control: private");
        header("Pragma: public");
        $date = date('Ymd');
        header("Content-Disposition: attachment; filename=" . $fileName . "_form_data_{$date}.csv");

        $fp = fopen('php://output', 'w');

        // write the columns
        $row = [
            t('Submitted Date'),
            t('User'),
        ];

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
            $row = [];
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
                    for ($i = 1; $i <= count($options); ++$i) {
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

        if ($this->request->get('action') == 'deleteForm') {
            if (!Loader::helper('validation/token')->validate('deleteForm')) {
                $this->error->add(t('Invalid Token.'));
            } else {
                $this->deleteForm($this->request->get('bID'), $this->request->get('qsID'));
            }
        }

        if ($this->request->get('action') == 'deleteFormAnswers') {
            if (!Loader::helper('validation/token')->validate('deleteFormAnswers')) {
                $this->error->add(t('Invalid Token.'));
            } else {
                $this->deleteFormAnswers($this->request->get('qsID'));
                $this->redirect('/dashboard/reports/forms');
            }
        }

        if ($this->request->get('action') == 'deleteResponse') {
            if (!Loader::helper('validation/token')->validate('deleteResponse')) {
                $this->error->add(t('Invalid Token.'));
            } else {
                $this->deleteAnswers($this->request->get('asid'));
            }
        }

        //load surveys
        $surveysRS = FormBlockStatistics::loadSurveys($tempMiniSurvey);

        //index surveys by question set id
        $surveys = [];
        while ($survey = $surveysRS->fetchRow()) {
            //get Survey Answers
            $survey['answerSetCount'] = MiniSurvey::getAnswerCount($survey['questionSetId']);
            $surveys[$survey['questionSetId']] = $survey;
        }

        //load requested survey response
        if ($this->request->get('qsid')) {
            $questionSet = intval(preg_replace('/[^[:alnum:]]/', '', $this->request->get('qsid')));

            //get Survey Questions
            $questionsRS = MiniSurvey::loadQuestions($questionSet);
            $questions = [];
            while ($question = $questionsRS->fetchRow()) {
                $questions[$question['msqID']] = $question;
            }

            //get Survey Answers
            $answerSetCount = MiniSurvey::getAnswerCount($questionSet);

            //pagination
            $pageBaseSurvey = $pageBase . '?qsid=' . $questionSet;
            $paginator = Loader::helper('pagination');
            $sortBy = $this->request->get('sortBy');
            $paginator->init(
                (int) $this->request->get('page'), $answerSetCount, $pageBaseSurvey . '&page=%pageNum%&sortBy=' . $sortBy,
                $this->pageSize
            );

            if ($this->pageSize > 0) {
                $limit = $paginator->getLIMIT();
            } else {
                $limit = '';
            }
            $answerSets = FormBlockStatistics::buildAnswerSetsArray($questionSet, $sortBy, $limit);
        } else {
            $questions = null;
            $answerSets = null;
            $paginator = null;
            $questionSet = null;
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
        $v = [intval($asID)];
        $q = 'DELETE FROM btFormAnswers WHERE asID = ?';
        $r = $db->query($q, $v);

        $q = 'DELETE FROM btFormAnswerSet WHERE asID = ?';
        $r = $db->query($q, $v);
    }

    //DELETE A FORM ANSWERS
    private function deleteFormAnswers($qsID)
    {
        $db = Loader::db();
        $v = [intval($qsID)];
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

        $v = [intval($bID)];
        $q = 'DELETE FROM btFormQuestions WHERE bID = ?';
        $r = $db->query($q, $v);

        $q = 'DELETE FROM btForm WHERE bID = ?';
        $r = $db->query($q, $v);

        $q = 'DELETE FROM Blocks WHERE bID = ?';
        $r = $db->query($q, $v);
    }
}
