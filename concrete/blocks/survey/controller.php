<?php
namespace Concrete\Block\Survey;

use Concrete\Core\Block\BlockController;
use Page;
use User;
use Core;
use Database;

class Controller extends BlockController
{
    public $options = [];
    protected $btTable = 'btSurvey';
    protected $btInterfaceWidth = 500;
    protected $btInterfaceHeight = 500;
    protected $btExportTables = ['btSurvey', 'btSurveyOptions', 'btSurveyResults'];

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
            $q = "SELECT optionID, optionName, displayOrder FROM btSurveyOptions WHERE bID = ? ORDER BY displayOrder ASC";
            $r = $db->query($q, $v);
            $this->options = [];
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
                    $this->cID, ];
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
            $v = [$u->getUserID(), $this->bID, $this->cID];
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
        $this->setPollOptions();

        $db = Database::connection();

        foreach ($this->options as $opt) {
            $v1 = [$newBID, $opt->getOptionName(), $opt->getOptionDisplayOrder()];
            $q1 = "INSERT INTO btSurveyOptions (bID, optionName, displayOrder) VALUES (?, ?, ?)";
            $db->query($q1, $v1);

            $v2 = [$opt->getOptionID()];
            $newOptionID = $db->Insert_ID();
            $q2 = "SELECT * FROM btSurveyResults WHERE optionID = ?";
            $r2 = $db->query($q2, $v2);
            if ($r2) {
                while ($row = $r2->fetchRow()) {
                    $v3 = [$newOptionID, $row['uID'], $row['ipAddress'], $row['timestamp']];
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

        if (!isset($args['survivingOptionNames']) || !is_array($args['survivingOptionNames'])) {
            $args['survivingOptionNames'] = [];
        }

        $slashedArgs = [];
        foreach ($args['survivingOptionNames'] as $arg) {
            $slashedArgs[] = addslashes($arg);
        }
        $db->query(
            "DELETE FROM btSurveyOptions WHERE optionName NOT IN ('" . implode(
                "','",
                $slashedArgs) . "') AND bID = " . intval($this->bID));

        $max = $db->getOne(
            "SELECT MAX(displayOrder) AS maxDisplayOrder FROM btSurveyOptions WHERE bID = " . intval($this->bID));

        $displayOrder = $max ? (int) $max + 1 : 0;

        if (isset($args['pollOption']) && is_array($args['pollOption'])) {
            foreach ($args['pollOption'] as $optionName) {
                $v1 = [$this->bID, $optionName, $displayOrder];
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
}
