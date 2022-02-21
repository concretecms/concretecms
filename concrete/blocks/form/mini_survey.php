<?php

namespace Concrete\Block\Form;

use Concrete\Core\Database\Connection\Connection;
use Doctrine\DBAL\ForwardCompatibility\DriverStatement;
use Doctrine\DBAL\Types\Types;

class MiniSurvey
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
     * @var int
     */
    public $lastSavedMsqID = 0;

    /**
     * @var int
     */
    public $lastSavedqID = 0;

    /**
     * @var bool
     */
    public $frontEndMode = false;

    /**
     * @var Connection
     */
    protected $db;

    /**
     * @var \Concrete\Core\Http\Request
     */
    protected $request;

    public function __construct()
    {
        $this->db = app(Connection::class);
        $this->request = app(\Concrete\Core\Http\Request::class);
    }

    /**
     * @param array<string, mixed> $values
     * @param int|bool $withOutput whether to echo out the output - old method set to 0/false to not output anything
     *
     * @throws \Doctrine\DBAL\Exception
     *
     * @return array<string, mixed> Array that can be json encoded
     */
    public function addEditQuestion($values, $withOutput = 1)
    {
        $jsonVals = [];
        $values['options'] = str_replace(["\r", "\n"], '%%', $values['options']);
        if (strtolower($values['inputType']) === 'undefined') {
            $values['inputType'] = 'field';
        }

        //set question set id, or create a new one if none exists
        if ((int) ($values['qsID']) === 0) {
            $values['qsID'] = time();
        }

        //validation
        if ($values['question'] === '' || $values['inputType'] === '' || $values['inputType'] === 'null') {
            //complete required fields
            $jsonVals['success'] = 0;
            $jsonVals['noRequired'] = 1;
        } else {
            if ((int) ($values['msqID'])) {
                $jsonVals['mode'] = 'Edit';

                //questions that are edited are given a placeholder row in btFormQuestions with bID=0, until a bID is assign on block update
                $pendingEditExists = $this->db->fetchOne('select count(*) as total from btFormQuestions where bID=0 AND msqID=' . (int) ($values['msqID']));

                //hideQID tells the interface to hide the old version of the question in the meantime
                $vals = [(int) ($values['msqID'])];
                $jsonVals['hideQID'] = (int) ($this->db->fetchOne('SELECT MAX(qID) FROM btFormQuestions WHERE bID!=0 AND msqID=?', $vals));
            } else {
                $jsonVals['mode'] = 'Add';
                $pendingEditExists = false;
            }

            //see if the 'send notification from' checkbox is checked and save this to the options field
            if ($values['inputType'] === 'email') {
                $options = [];
                if (array_key_exists('send_notification_from', $values) && $values['send_notification_from'] == 1) {
                    $options['send_notification_from'] = 1;
                } else {
                    $options['send_notification_from'] = 0;
                }
                $values['options'] = serialize($options);
            }
            if ($pendingEditExists) {
                $width = $height = 0;
                if ($values['inputType'] === 'text') {
                    $width = $this->limitRange((int) ($values['width']), 20, 500);
                    $height = $this->limitRange((int) ($values['height']), 1, 100);
                }
                $dataValues = [
                    (int) ($values['qsID']),
                    trim($values['question']),
                    $values['inputType'],
                    $values['options'],
                    (int) ($values['position']),
                    $width,
                    $height,
                    (int) ($values['required']),
                    $values['defaultDate'],
                    (int) ($values['msqID']),
                ];
                $sql = 'UPDATE btFormQuestions SET questionSetId=?, question=?, inputType=?, options=?, position=?, width=?, height=?, required=?, defaultDate=? WHERE msqID=? AND bID=0';
            } else {
                if (!isset($values['position'])) {
                    $values['position'] = 1000;
                }
                if (!(int) ($values['msqID'])) {
                    $values['msqID'] = ($this->db->fetchOne('SELECT MAX(msqID) FROM btFormQuestions') + 1);
                }
                $dataValues = [
                    $values['msqID'],
                    (int) ($values['qsID']),
                    trim($values['question']),
                    $values['inputType'],
                    $values['options'],
                    (int) ($values['position']),
                    (int) ($values['width']),
                    (int) ($values['height']),
                    (int) ($values['required']),
                    $values['defaultDate'],
                ];
                $sql = 'INSERT INTO btFormQuestions (msqID,questionSetId,question,inputType,options,position,width,height,required,defaultDate) VALUES (?,?,?,?,?,?,?,?,?,?)';
            }
            $this->db->executeStatement($sql, $dataValues);
            $this->lastSavedMsqID = (int) ($values['msqID']);
            $this->lastSavedqID = (int) ($this->db->fetchOne('SELECT MAX(qID) FROM btFormQuestions WHERE bID=0 AND msqID=?', [$values['msqID']]));
            $jsonVals['qID'] = $this->lastSavedqID;
            $jsonVals['success'] = 1;
        }

        $jsonVals['qsID'] = $values['qsID'];
        $jsonVals['msqID'] = (int) ($values['msqID']);
        //create json response object - old method
        $jsonPairs = [];
        foreach ($jsonVals as $key => $val) {
            if ($key === 'mode') {
              $val = '"' . $val . '"';
            }
            $jsonPairs[] = $key . ':' . $val;
        }
        if ($withOutput) {
            echo '{' . implode(',', $jsonPairs) . '}';
        }

        return $jsonVals;
    }

    /**
     * @param int $qsID
     * @param int $qID
     * @param bool $asJson will return the data as an array to be json encoded
     *
     * @throws \Doctrine\DBAL\Exception
     * @throws \Doctrine\DBAL\Driver\Exception
     *
     * @return array<string, mixed>
     */
    public function getQuestionInfo($qsID, $qID, $asJson = false)
    {
        $questionRS = $this->db->executeQuery('SELECT * FROM btFormQuestions WHERE questionSetId=' . (int) $qsID . ' AND qID=' . (int) $qID . ' LIMIT 1');
        $questionRow = $questionRS->fetchAssociative();
        $jsonPairs = [];
        $jsonObject = [];
        if (is_array($questionRow)) {
            /**
             * @var string $key
             * @var mixed $val
             */
            foreach ($questionRow as $key => $val) {
            if ($key === 'options') {
                $key = 'optionVals';
                if ($questionRow['inputType'] === 'email') {
                    $options = unserialize($val, [false]);
                    if (is_array($options)) {
                        foreach ($options as $o_key => $o_val) {
                            $val = $o_key . '::' . $o_val . ';';
                        }
                    }
                }
            }
            $jsonObject[$key] = $val;

            $jsonPairs[] = $key . ':"' . str_replace(["\r", "\n"], '%%', addslashes($val)) . '"';
        }
        }
        if (!$asJson) {
            echo '{' . implode(',', $jsonPairs) . '}';
        }

        return $jsonObject;
    }

    /**
     * @param int $qsID
     * @param int $msqID
     *
     * @throws \Doctrine\DBAL\Exception
     *
     * @return void
     */
    public function deleteQuestion($qsID, $msqID)
    {
        $this->db->delete('btFormQuestions', ['questionSetId' => $qsID, 'msqID' => $msqID, 'bID' => 0], [Types::INTEGER, Types::INTEGER, Types::INTEGER]);
    }

    /**
     * @param int $qsID
     * @param int $bID
     * @param int $showPending
     *
     * @throws \Doctrine\DBAL\Exception
     *
     * @return DriverStatement<mixed>|false
     */
    public function loadQuestions($qsID, $bID = 0, $showPending = 0)
    {
        $queryBuilder = $this->db->createQueryBuilder();
        $queryBuilder->select('*')->from('btFormQuestions')->where($queryBuilder->expr()->eq('questionSetId', ':qsID'))->setParameter('qsID', $qsID, Types::INTEGER)->orderBy('position')->addOrderBy('msqID');

        if ((int) $bID) {
            $expr = $queryBuilder->expr()->eq('bID', ':bID');
            if ($showPending) {
                $expr = $queryBuilder->expr()->or($expr, $queryBuilder->expr()->eq('bID', 0));
            }
            $queryBuilder->andWhere($expr);
            $queryBuilder->setParameter('bID', $bID, Types::INTEGER);
        }

        return $queryBuilder->execute();
    }

    /**
     * @param int $qsID
     *
     * @throws \Doctrine\DBAL\Exception
     *
     * @return mixed
     */
    public static function getAnswerCount($qsID)
    {
        /** @var Connection $db */
        $db = app(Connection::class);

        return $db->fetchOne('SELECT count(*) FROM btFormAnswerSet WHERE questionSetId=' . (int) $qsID);
    }

    /**
     * @param int $qsID
     * @param bool $showEdit
     * @param int $bID
     * @param int[]|string[] $hideQIDs
     * @param int|bool $showPending
     * @param int|bool $editmode
     *
     * @throws \Doctrine\DBAL\Exception
     *
     * @return void
     */
    public function loadSurvey($qsID, $showEdit = false, $bID = 0, $hideQIDs = [], $showPending = 0, $editmode = 0)
    {
        //loading questions
        $questionsRS = $this->loadQuestions($qsID, $bID, $showPending);

        if (!$showEdit) {
            echo '<div>';
            while ($questionRow = $questionsRS->fetch()) {
                if (in_array($questionRow['qID'], $hideQIDs)) {
                    continue;
                }
                // this special view logic for the checkbox list isn't doing it for me
                /*
                if ($questionRow['inputType'] == 'checkboxlist' && strpos($questionRow['options'], '%%') === false) {
                    echo '<tr>
                        <td valign="top" colspan="2" class="question">
                            <div class="checkboxItem">
                                <div class="checkboxPair">'.$this->loadInputType($questionRow,$showEdit).$questionRow['question'].'</div>
                            </div>
                        </td>
                    </tr>';
                } else {
                 */
                $requiredSymbol = ($questionRow['required']) ? '&nbsp;<span class="required">*</span>' : '';
                echo '<div class="form-group">
                    <label class="control-label form-label" for="Question' . (int) ($questionRow['msqID']) . '">' . $questionRow['question'] . '' . $requiredSymbol . '</label></td>
                    <div>' . $this->loadInputType($questionRow, $showEdit) . '</div>
                </div>';
                //}
            }
            $surveyBlockInfo = $this->getMiniSurveyBlockInfoByQuestionId($qsID, (int) $bID);
            if (empty($surveyBlockInfo['submitText'])) {
                $surveyBlockInfo['submitText'] = 'Submit';
            }

            if (!empty($surveyBlockInfo['displayCaptcha'])) {
                echo '<div class="ccm-edit-mode-disabled-item">' . t('Form Captcha') . '</div><br/>';
            }

            if ($editmode) {
                echo '<div class="form-group"><input class="btn btn-primary" name="Submit" type="button" value="' . t($surveyBlockInfo['submitText']) . '" /></div>'; //make this a button
            } else {
                echo '<div class="form-group"><input class="btn btn-primary" name="Submit" type="submit" value="' . t($surveyBlockInfo['submitText']) . '" /></div>';
            }
            echo '</div>';
        } else {
            echo '<div id="miniSurveyTableWrap"><ul id="miniSurveyPreviewTable" class="list-group">';
            while ($questionRow = $questionsRS->fetch()) {
                if (in_array($questionRow['qID'], $hideQIDs)) {
                    continue;
                }

                $requiredSymbol = ($questionRow['required']) ? '<span class="required">*</span>' : '';
                ?>
					<li id="miniSurveyQuestionRow<?php echo $questionRow['msqID']?>" class="miniSurveyQuestionRow list-group-item">
						<div class="miniSurveyQuestion"><?php echo $questionRow['question'] . ' ' . $requiredSymbol?></div>
						<?php  /* <div class="miniSurveyResponse"><?php echo $this->loadInputType($questionRow,$showEdit)?></div> */ ?>
						<div class="miniSurveyOptions">
							<a href="javascript:void(0)" class="ccm-icon-wrapper" onclick="miniSurvey.moveUp(this,<?php echo $questionRow['msqID']?>);return false"><i class="fas fa-chevron-up"></i></a>
							<a href="javascript:void(0)" class="ccm-icon-wrapper" onclick="miniSurvey.moveDown(this,<?php echo $questionRow['msqID']?>);return false"><i class="fas fa-chevron-down"></i></a>
							<a href="javascript:void(0)" class="ccm-icon-wrapper" onclick="miniSurvey.reloadQuestion(<?=(int) ($questionRow['qID']) ?>);return false"><i class="fas fa-pencil-alt"></i></a>
							<a href="javascript:void(0)" class="ccm-icon-wrapper" onclick="miniSurvey.deleteQuestion(this,<?=(int) ($questionRow['msqID']) ?>,<?=(int) ($questionRow['qID'])?>);return false"><i class="fas fa-trash"></i></a>
						</div>
						<div class="clearfix"></div>
					</li>
				<?php
            }
            echo '</div></div>';
        }
    }

    /**
     * @param array<string,mixed> $questionData
     * @param int|bool $showEdit
     * @param bool $hasError does this input type have an error? if true adds the is-invalid class
     *
     * @return string
     */
    public function loadInputType($questionData, $showEdit, $hasError = false)
    {
        $options = explode('%%', $questionData['options']);
        $defaultDate = $questionData['defaultDate'];
        $msqID = (int) ($questionData['msqID']);
        $datetime = app('helper/form/date_time');
        $html = '';
        $errorClass = $hasError ? ' is-invalid' : '';
        $val = $this->request->get('Question' . $msqID);
        switch ($questionData['inputType']) {
            case 'checkboxlist':
                $html .= '<div class="checkboxList">' . "\r\n";
                for ($i = 0, $iMax = count($options); $i < $iMax; $i++) {
                    if (trim($options[$i]) === '') {
                        continue;
                    }
                    $checked = ($this->request->get('Question' . $msqID . '_' . $i) == trim($options[$i])) ? 'checked' : '';
                    $html .= '  <div class="form-check"><input id="question_' . $msqID . '_' . $i . ($showEdit ? '_preview' : '') . '" name="Question' . $msqID . '_' . $i . '" class="form-check-input' . $errorClass . '" type="checkbox" value="' . trim($options[$i]) . '" ' . $checked . ' /><label class="form-check-label" for="question_' . $msqID . '_' . $i . ($showEdit ? '_preview' : '') . '"> <span>' . $options[$i] . '</span></label></div>' . "\r\n";
                }
                $html .= '</div>';

                return $html;
            case 'select':
                if ($this->frontEndMode) {
                    $selected = (!$val) ? 'selected="selected"' : '';
                    $html .= '<option value="" ' . $selected . '>----</option>';
                }
                foreach ($options as $option) {
                    $checked = ($val == trim($option)) ? 'selected="selected"' : '';
                    $html .= '<option ' . $checked . '>' . trim($option) . '</option>';
                }

                return '<select class="form-control' . $errorClass . '" name="Question' . $msqID . '" id="Question' . $msqID . '" >' . $html . '</select>';
            case 'radios':
                $index = 1;
                foreach ($options as $option) {
                    if (trim($option) === '') {
                        continue;
                    }
                    $checked = ($val == trim($option)) ? 'checked' : '';
                    $html .= '<div class="form-check"><input class="form-check-input' . $errorClass . '" id="Question' . $msqID . '_' . $index . '" name="Question' . $msqID . '" type="radio" value="' . trim($option) . '" ' . $checked . ' /><label for="Question' . $msqID . '_' . $index . '" class="form-check-label"> <span>' . $option . '</span></label></div>';
                    $index++;
                }

                return $html;
            case 'fileupload':
                return '<input type="file" name="Question' . $msqID . '" class="form-control' . $errorClass . '" id="Question' . $msqID . '" />';
            case 'text':
                $val = ($val) ? app('helper/text')->entities($val) : '';

                return '<textarea name="Question' . $msqID . '" class="form-control' . $errorClass . '" id="Question' . $msqID . '" cols="' . $questionData['width'] . '" rows="' . $questionData['height'] . '">' . $val . '</textarea>';
            case 'url':
                $val = $val ?: '';

                return '<input name="Question' . $msqID . '" id="Question' . $msqID . '" class="form-control' . $errorClass . '" type="url" value="' . stripslashes(htmlspecialchars($val)) . '" />';
            case 'telephone':
                $val = $val ?: '';

                return '<input name="Question' . $msqID . '" id="Question' . $msqID . '" class="form-control' . $errorClass . '" type="tel" value="' . stripslashes(htmlspecialchars($val)) . '" />';
            case 'email':
                $val = $val ?: '';

                return '<input name="Question' . $msqID . '" id="Question' . $msqID . '" class="form-control' . $errorClass . '" type="email" value="' . stripslashes(htmlspecialchars($val)) . '" />';
            case 'date':
                $val = $val ?: $defaultDate;
                $return = $datetime->date('Question' . $msqID, $val);

                return $hasError ? str_replace('class="form-control ccm-input-date', 'class="form-control ccm-input-date is-invalid', $return) : $return;
            case 'datetime':
                if (!$val) {
                    if (
                        $this->request->get('Question' . $msqID . '_dt') && $this->request->get('Question' . $msqID . '_h')
                        && $this->request->get('Question' . $msqID . '_m') && $this->request->get('Question' . $msqID . '_a')
                    ) {
                        $val = $this->request->get('Question' . $msqID . '_dt') . ' ' . $this->request->get('Question' . $msqID . '_h')
                            . ':' . $this->request->get('Question' . $msqID . '_m') . ' ' . $this->request->get('Question' . $msqID . '_a');
                    } else {
                        $val = $defaultDate;
                    }
                }
                $return = $datetime->datetime('Question' . $msqID, $val);
                // only way to add the is-invalid from bs5
                return $hasError ? str_replace('class="form-control ccm-input-date', 'class="form-control ccm-input-date is-invalid', $return) : $return;
            case 'field':
            default:
                $val = $val ?: '';

                return '<input name="Question' . $msqID . '" id="Question' . $msqID . '" class="form-control' . $errorClass . '" type="text" value="' . stripslashes(htmlspecialchars($val)) . '" />';
        }
    }

    /**
     * @param int $bID
     *
     * @throws \Doctrine\DBAL\Exception
     * @throws \Doctrine\DBAL\Driver\Exception
     *
     * @return array<string,mixed>
     */
    public function getMiniSurveyBlockInfo($bID)
    {
        $rs = $this->db->executeQuery('SELECT * FROM btForm WHERE bID=' . (int) $bID . ' LIMIT 1');

        return $rs->fetchAssociative() ?: [];
    }

    /**
     * @param int $qsID
     * @param int $bID
     *
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Doctrine\DBAL\Exception
     *
     * @return array<string, mixed>|false
     */
    public function getMiniSurveyBlockInfoByQuestionId($qsID, $bID = 0)
    {
        $queryBuilder = $this->db->createQueryBuilder();
        $queryBuilder->select('*')->from('btForm')->where($queryBuilder->expr()->eq('questionSetId', ':qsID'))->setParameter('qsID', (int) $qsID, Types::INTEGER);

        if ((int) $bID > 0) {
            $queryBuilder->andWhere($queryBuilder->expr()->eq('bID', ':bID'))->setParameter('bID', (int) $bID, Types::INTEGER);
        }
        $queryBuilder->setMaxResults(1);
        $results = $queryBuilder->execute()->fetchAssociative();

        return $results ?: [];
    }

    /**
     * @param int $qsID
     * @param string $qIDs
     *
     * @throws \Doctrine\DBAL\Exception
     *
     * @return void
     */
    public function reorderQuestions($qsID = 0, $qIDs = '')
    {
        $qIDArray = explode(',', $qIDs);
        if (!is_array($qIDArray)) {
            $qIDArray = [$qIDArray];
        }
        $positionNum = 0;
        foreach ($qIDArray as $qID) {
            $this->db->update('btFormQuestions', ['position' => $positionNum], ['msqID' => (int) $qID, 'questionSetId' => (int) $qsID], [Types::INTEGER, Types::INTEGER, Types::INTEGER]);
            $positionNum++;
        }
    }

    /**
     * @param int $val
     * @param int $min
     * @param int $max
     *
     * @return int
     */
    public function limitRange($val, $min, $max)
    {
        $val = ($val < $min) ? $min : $val;

        return ($val > $max) ? $max : $val;
    }

    //Run on Form block edit

    /**
     * @param int $qsID
     * @param int $bID
     *
     * @throws \Doctrine\DBAL\Exception
     *
     * @return void
     */
    public static function questionCleanup($qsID = 0, $bID = 0)
    {
        /** @var Connection $db */
        $db = app(Connection::class);

        //First make sure that the bID column has been set for this questionSetId (for backwards compatibility)
        $vals = [(int) $qsID];
        $questionsWithBIDs = $db->fetchOne('SELECT count(*) FROM btFormQuestions WHERE bID!=0 AND questionSetId=? ', $vals);

        //form block was just upgraded, so set the bID column
        if (!$questionsWithBIDs) {
            $db->update('btFormQuestions', ['bID' => (int) $bID], ['bID' => 0, 'questionSetId' => (int) $qsID], [Types::INTEGER, Types::INTEGER]);

            return;
        }

        //Then remove all temp/placeholder questions for this questionSetId that haven't been assigned to a block
        $db->delete('btFormQuestions', ['bID' => 0, 'questionSetId' => (int) $qsID], [Types::INTEGER]);
    }
}
