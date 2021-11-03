<?php

namespace Concrete\Controller\SinglePage\Dashboard\Reports;

use Concrete\Block\Survey\SurveyList;
use Concrete\Core\Page\Controller\DashboardPageController;

class Surveys extends DashboardPageController
{
    public function formatDate($inputTime)
    {
        if (empty($inputTime)) {
            return '';
        }
        $dh = $this->app->make('helper/date'); // @var $dh \Concrete\Core\Localization\Service\Date

        return $dh->formatPrettyDateTime($inputTime);
    }

    public function viewDetail($bID = 0, $cID = 0)
    {
        // If a valid bID and cID are set, get the corresponding data
        if ($bID > 0 && $cID > 0) {
            $this->getSurveyDetails($bID, $cID);
            $this->displayChart($bID, $cID);
        } else { // Otherwise, redirect the page to overview
            return $this->buildRedirect('/dashboard/reports/surveys');
        }
    }

    public function view()
    {
        $sl = new SurveyList();
        $slResults = $sl->getPage();

        // Store data in variable stored in larger scope
        $this->set('surveys', $slResults);
        $this->set('surveyList', $sl);
    }

    public function getSurveyDetails($bID, $cID)
    {
        // Load the data from the database
        $db = $this->app->make('database/connection');

        $v = [(int) $bID, (int) $cID];
        $q =
            'SELECT
                  btSurveyOptions.optionName,
                   Users.uName,
                   ipAddress,
                   timestamp,
                   question
             FROM
                  btSurveyResults
                  INNER JOIN btSurveyOptions ON btSurveyOptions.optionId = btSurveyResults.optionID
                  INNER JOIN btSurvey ON btSurvey.bID = btSurveyResults.bID
                  LEFT JOIN Users ON Users.uID = btSurveyResults.uID
			 WHERE
				btSurveyResults.bID = ? AND btSurveyResults.cId = ?';
        $r = $db->query($q, $v);

        // Set default information in case query returns nothing
        $current_survey = 'Unknown Survey';
        $details = [];

        foreach ($r as $row) {
            $details[] = [
                'option' => $row['optionName'],
                'ipAddress' => $row['ipAddress'],
                'date' => $this->formatDate($row['timestamp']),
                'user' => $row['uName'],
            ];

            $current_survey = $row['question'];
        }

        if (!count($details)) {
            // If there is no user-submitted information pertaining to this survey, just get the name
            $q = 'SELECT question FROM btSurvey WHERE bID = ?';
            $v = [$bID];
            $r = $db->query($q, $v);
            if ($row = $r->fetch()) {
                $current_survey = $row['question'];
            }
        }
        // Store local data in larger scope
        $this->set('survey_details', $details);
        $this->set('current_survey', $current_survey);
    }

    protected function displayChart($bID, $cID)
    {
        // Prepare the database query
        $db = $this->app->make('database/connection');

        // Get all available options
        $options = [];
        $v = [(int) $bID];
        $q = 'SELECT optionID, optionName FROM btSurveyOptions WHERE bID = ? ORDER BY displayOrder ASC';
        $r = $db->executeQuery($q, $v);

        $i = 0;
        while ($row = $r->fetch()) {
            $options[$i]['name'] = $row['optionName'];
            $options[$i]['id'] = $row['optionID'];
            $i++;
        }

        // Get chosen count for each option
        $total_results = 0;
        $i = 0;
        foreach ($options as $option) {
            $v = [$option['id'], (int) $bID, (int) $cID];
            $q = 'SELECT count(*) FROM btSurveyResults WHERE optionID = ? AND bID = ? AND cID = ?';
            $r = $db->executeQuery($q, $v);

            if ($row = $r->fetch()) {
                $options[$i]['amount'] = $row['count(*)'];
                $total_results += $row['count(*)'];
            }
            $i++;
        }

        if ($total_results <= 0) {
            $chart_options = '<div style="text-align: center; margin-top: 15px;">' . t(
                'No data is available yet.'
            ) . '</div>';
            $this->set('chart_options', $chart_options);

            return;
        }

        // Convert option counts to percentages, initiate colors
        $availableChartColors = [
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
        ];
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
            $graphColors
        ) . '" />';
        $this->set('pie_chart', $img_src);

        // Build human-readable option list
        $i = 1;
        $chart_options = '<table class="table"><tbody>';
        foreach ($options as $option) {
            $chart_options .= '<tr>';
            $chart_options .= '<td>';
            $chart_options .= '<strong>' . trim(h($options[$i - 1]['name'])) . '</strong>';
            $chart_options .= '</td>';
            $chart_options .= '<td style="text-align:right; white-space: nowrap">';
            $chart_options .= ($option['amount'] > 0) ? round($option['amount'] / $total_results * 100) : 0;
            $chart_options .= '%';
            $chart_options .= '<div class="surveySwatch" style="border-radius: 3px; margin-left: 6px; width:18px; height:18px; float:right; background:#' . $graphColors[$i - 1] . '"></div>';
            $chart_options .= '</td>';
            $chart_options .= '</tr>';
            $i++;
        }
        $chart_options .= '</tbody></table>';
        $this->set('chart_options', $chart_options);
    }
}
