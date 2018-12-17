<?php
namespace Concrete\Controller\SinglePage\Dashboard\Reports\Forms;

use Concrete\Core\File\File;
use Concrete\Core\Page\Controller\DashboardPageController;
use Symfony\Component\HttpFoundation\RedirectResponse;
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
        /* @var $dateHelper \Concrete\Core\Localization\Service\Date */
        $dateHelper = $this->app->make('helper/date');

        $this->pageSize = 0;
        $this->loadSurveyResponses();
        $textHelper = $this->app->make('helper/text');

        $questionSet = $this->get('questionSet');
        $answerSets = $this->get('answerSets');
        $questions = $this->get('questions');
        $surveys = $this->get('surveys');

        $escapeCharacter = "'";
        $charactersToEscape = ['-', '+', '='];

        $fileName = $textHelper->filterNonAlphaNum($surveys[$questionSet]['surveyName']);

        header('Content-Type: text/csv');
        header('Cache-control: private');
        header('Pragma: public');
        $date = date('Ymd');
        header('Content-Disposition: attachment; filename=' . $fileName . "_form_data_{$date}.csv");

        $fp = fopen('php://output', 'w');

        // write the columns
        $row = [
            t('Submitted Date'),
            t('User'),
        ];

        foreach ($questions as $questionId => $question) {
            if ($question['inputType'] === 'checkboxlist') {
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
                if ($question['inputType'] === 'checkboxlist') {
                    $options = explode('%%', $question['options']);
                    $subanswers = explode(',', $answerSet['answers'][$questionId]['answer']);
                    for ($i = 1, $iMax = count($options); $i <= $iMax; ++$i) {
                        if (in_array(trim($options[$i - 1]), $subanswers)) {
                            $row[] = 'x';
                        } else {
                            $row[] = '';
                        }
                    }
                } else if ($question['inputType'] === 'fileupload') {
                    $fID = (int)$answerSet['answers'][$questionId]['answer'];
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

            fputcsv($fp, $row);
        }

        fclose($fp);
        die;
    }

    private function loadSurveyResponses()
    {
        $c = Page::getCurrentPage();
        $tempMiniSurvey = new MiniSurvey();
        $pageBase = \URL::to($c);

        if ($this->request->get('action') === 'deleteForm') {
            if (!$this->token->validate('deleteForm')) {
                $this->error->add(t('Invalid Token.'));
            } else {
                $this->deleteForm($this->request->get('bID'), $this->request->get('qsID'));
            }
        }

        if ($this->request->get('action') === 'deleteFormAnswers') {
            if (!$this->token->validate('deleteFormAnswers')) {
                $this->error->add(t('Invalid Token.'));
            } else {
                $this->deleteFormAnswers($this->request->get('qsID'));
                RedirectResponse::create('/dashboard/reports/forms')->send();
                $this->app->shutdown();
            }
        }

        if ($this->request->get('action') === 'deleteResponse') {
            if (!$this->token->validate('deleteResponse')) {
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
            $questionSet = (int)preg_replace('/[^[:alnum:]]/', '', $this->request->get('qsid'));

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
            $paginator = $this->app->make('helper/pagination');
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
        $db = $this->app->make('database')->connection();
        $v = [(int)$asID];
        $q = 'DELETE FROM btFormAnswers WHERE asID = ?';
        $db->query($q, $v);

        $q = 'DELETE FROM btFormAnswerSet WHERE asID = ?';
        $db->query($q, $v);
    }

    //DELETE A FORM ANSWERS
    private function deleteFormAnswers($qsID)
    {
        $db = $this->app->make('database')->connection();
        $v = [(int)$qsID];
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
        $db = $this->app->make('database')->connection();
        $this->deleteFormAnswers($qsID);

        $v = [(int)$bID];
        $q = 'DELETE FROM btFormQuestions WHERE bID = ?';
        $db->query($q, $v);

        $q = 'DELETE FROM btForm WHERE bID = ?';
        $db->query($q, $v);

        $q = 'DELETE FROM Blocks WHERE bID = ?';
        $db->query($q, $v);
    }
}
