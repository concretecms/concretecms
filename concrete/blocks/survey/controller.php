<?php

namespace Concrete\Block\Survey;

use Concrete\Core\Block\BlockController;
use Page;
use User;
use Core;
use Database;

class Controller extends BlockController
{
    public $options = array();
    protected $btTable = 'btSurvey';
    protected $btInterfaceWidth = "420";
    protected $btInterfaceHeight = "400";
    protected $btExportTables = array('btSurvey', 'btSurveyOptions', 'btSurveyResults');

    public function on_start()
    {
        $this->cID = null;

        $c = Page::getCurrentPage();

        if (is_object($c)) {
            $this->cID = $c->getCollectionID();
        }
        if ($this->bID) {
            $db = Database::connection();
            $v = array($this->bID);
            $q = "SELECT optionID, optionName, displayOrder FROM btSurveyOptions WHERE bID = ? ORDER BY displayOrder ASC";
            $r = $db->query($q, $v);
            $this->options = array();
            if ($r) {
                while ($row = $r->fetchRow()) {
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

    /**
     * Used for localization. If we want to localize the name/description we have to include this.
     */
    public function getBlockTypeDescription()
    {
        return t("Provide a simple survey, along with results in a pie chart format.");
    }

    public function getBlockTypeName()
    {
        return t("Survey");
    }

    public function getQuestion()
    {
        return $this->question;
    }

    public function getPollOptions()
    {
        return $this->options;
    }

    public function delete()
    {
        $db = Database::connection();
        $v = array($this->bID);

        $q = "DELETE FROM btSurveyOptions WHERE bID = ?";
        $db->query($q, $v);

        $q = "DELETE FROM btSurveyResults WHERE bID = ?";
        $db->query($q, $v);

        parent::delete();
    }

    public function action_form_save_vote($bID = false)
    {
        if ($this->bID != $bID) {
            return false;
        }

        $u = new User();
        $db = Database::connection();
        $bo = $this->getBlockObject();
        if ($this->post('rcID')) {
            // we pass the rcID through the form so we can deal with stacks
            $c = Page::getByID($this->post('rcID'));
        } else {
            $c = $this->getCollectionObject();
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
                $v = array(
                    $_REQUEST['optionID'],
                    $this->bID,
                    $duID,
                    $ip,
                    $this->cID, );
                $q = "INSERT INTO btSurveyResults (optionID, bID, uID, ipAddress, cID) VALUES (?, ?, ?, ?, ?)";
                $db->query($q, $v);
                setcookie("ccmPoll" . $this->bID . '-' . $this->cID, "voted", time() + 1296000, DIR_REL . '/');
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
        $u = new User();
        if ($u->isRegistered()) {
            $db = Database::connection();
            $v = array($u->getUserID(), $this->bID, $this->cID);
            $q = "SELECT count(resultID) AS total FROM btSurveyResults WHERE uID = ? AND bID = ? AND cID = ?";
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
        $db = Database::connection();

        foreach ($this->options as $opt) {
            $v1 = array($newBID, $opt->getOptionName(), $opt->getOptionDisplayOrder());
            $q1 = "INSERT INTO btSurveyOptions (bID, optionName, displayOrder) VALUES (?, ?, ?)";
            $db->query($q1, $v1);

            $v2 = array($opt->getOptionID());
            $newOptionID = $db->Insert_ID();
            $q2 = "SELECT * FROM btSurveyResults WHERE optionID = ?";
            $r2 = $db->query($q2, $v2);
            if ($r2) {
                while ($row = $r2->fetchRow()) {
                    $v3 = array($newOptionID, $row['uID'], $row['ipAddress'], $row['timestamp']);
                    $q3 = "INSERT INTO btSurveyResults (optionID, uID, ipAddress, timestamp) VALUES (?, ?, ?, ?)";
                    $db->query($q3, $v3);
                }
            }
        }

        return parent::duplicate($newBID);
    }

    public function save($args)
    {
        parent::save($args);
        $db = Database::connection();

        if (!is_array($args['survivingOptionNames'])) {
            $args['survivingOptionNames'] = array();
        }

        $slashedArgs = array();
        foreach ($args['survivingOptionNames'] as $arg) {
            $slashedArgs[] = addslashes($arg);
        }
        $db->query(
            "DELETE FROM btSurveyOptions WHERE optionName NOT IN ('" . implode(
                "','",
                $slashedArgs) . "') AND bID = " . intval($this->bID));

        if (is_array($args['pollOption'])) {
            $displayOrder = 0;
            foreach ($args['pollOption'] as $optionName) {
                $v1 = array($this->bID, $optionName, $displayOrder);
                $q1 = "INSERT INTO btSurveyOptions (bID, optionName, displayOrder) VALUES (?, ?, ?)";
                $db->query($q1, $v1);
                ++$displayOrder;
            }
        }

        $query = "DELETE FROM btSurveyResults
			WHERE optionID NOT IN (
				SELECT optionID FROM btSurveyOptions WHERE bID = {$this->bID}
				)
			AND bID = {$this->bID} ";
        $db->query($query);
    }

    public function displayChart($bID, $cID)
    {
        // Prepare the database query
        $db = Database::connection();

        // Get all available options
        $options = array();
        $v = array(intval($bID));
        $q = 'SELECT optionID, optionName FROM btSurveyOptions WHERE bID = ? ORDER BY displayOrder ASC';
        $r = $db->Execute($q, $v);

        $i = 0;
        while ($row = $r->fetchRow()) {
            $options[$i]['name'] = $row['optionName'];
            $options[$i]['id'] = $row['optionID'];
            ++$i;
        }

        // Get chosen count for each option
        $total_results = 0;
        $i = 0;
        foreach ($options as $option) {
            $v = array($option['id'], intval($bID), intval($cID));
            $q = 'SELECT count(*) FROM btSurveyResults WHERE optionID = ? AND bID = ? AND cID = ?';
            $r = $db->Execute($q, $v);

            if ($row = $r->fetchRow()) {
                $options[$i]['amount'] = $row['count(*)'];
                $total_results += $row['count(*)'];
            }
            ++$i;
        }

        if ($total_results <= 0) {
            $chart_options = '<div style="text-align: center; margin-top: 15px;">' . t(
                    'No data is available yet.') . '</div>';
            $this->set('chart_options', $chart_options);

            return;
        }

        // Convert option counts to percentages, initiate colors
        $availableChartColors = array(
            '00CCdd',
            'cc3333',
            '330099',
            'FF6600',
            '9966FF',
            'dd7700',
            '66DD00',
            '6699FF',
            'FFFF33',
            'FFCC33',
            '00CCdd',
            'cc3333',
            '330099',
            'FF6600',
            '9966FF',
            'dd7700',
            '66DD00',
            '6699FF',
            'FFFF33',
            'FFCC33',
        );
        $percentage_value_string = '';
        foreach ($options as $option) {
            $option['amount'] /= $total_results;
            $percentage_value_string .= round($option['amount'], 3) . ',';
            $graphColors[] = array_pop($availableChartColors);
        }

        // Strip off trailing comma
        $percentage_value_string = substr_replace($percentage_value_string, '', -1);

        // Get Google Charts API image
        $img_src = '<img class="surveyChart" style="margin-bottom:10px;" border="" src="//chart.apis.google.com/chart?cht=p&chd=t:' . $percentage_value_string . '&chs=180x180&chco=' . implode(
                ',',
                $graphColors) . '" />';
        $this->set('pie_chart', $img_src);

        // Build human-readable option list
        $i = 1;
        $chart_options = '<table class="table"><tbody>';
        foreach ($options as $option) {
            $chart_options .= '<tr>';
            $chart_options .= '<td>';
            $chart_options .= '<strong>' . trim($options[$i - 1]['name']) . '</strong>';
            $chart_options .= '</td>';
            $chart_options .= '<td style="text-align:right; white-space: nowrap">';
            $chart_options .= ($option['amount'] > 0) ? round($option['amount'] / $total_results * 100) : 0;
            $chart_options .= '%';
            $chart_options .= '<div class="surveySwatch" style="border-radius: 3px; margin-left: 6px; width:18px; height:18px; float:right; background:#' . $graphColors[$i - 1] . '"></div>';
            $chart_options .= '</td>';
            $chart_options .= '</tr>';
            ++$i;
        }
        $chart_options .= '</tbody></table>';
        $this->set('chart_options', $chart_options);
    }
}
