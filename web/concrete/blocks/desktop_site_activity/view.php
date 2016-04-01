<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<div class="ccm-block-desktop-site-activity">

    <?php
    $c = Page::getCurrentPage();
    if ($c->isEditMode()) {
    ?>
    <div class="ccm-edit-mode-disabled-item"><?= t('Site Activity disabled in edit mode.')?></div>

    <?php } else { ?>
        <?php if (count($types)) { ?>
            <script type="text/javascript">
                $(function() {
                    var width = parseInt($('#ccm-block-desktop-site-activity-chart').width()),
                        height = width;

                    google.charts.load("current", {packages:["corechart"]});
                    google.charts.setOnLoadCallback(drawChart);
                    function drawChart() {

                        var results = [['<?=t('Item')?>', '<?=t('Number')?>']];
                        var slices = [];
                        <?php if (in_array('form_submissions', $types)) { ?>
                            results.push(['<?=t('Form Results')?>', <?=intval($formResults)?>]);
                            slices.push({color: '#00d0f8'});
                        <?php } ?>
                        <?php if (in_array('survey_results', $types)) { ?>
                            results.push(['<?=t('Survey Results')?>', <?=intval($surveyResults)?>]);
                            slices.push({color: '#7c33b1'});
                        <?php } ?>
                        <?php if (in_array('signups', $types)) { ?>
                            results.push(['<?=t('Signups')?>', <?=intval($signups)?>]);
                            slices.push({color: '#fed24b'});
                        <?php } ?>
                        <?php if (in_array('conversation_messages', $types)) { ?>
                            results.push(['<?=t('Conversation Messages')?>', <?=intval($messages)?>]);
                            slices.push({color: '#00cc66'});
                        <?php } ?>


                        var data = google.visualization.arrayToDataTable(results);

                        var options = {
                            pieHole: 0.9,
                            pieSliceText: 'none',
                            chartArea: {
                                left: 'auto',
                                width: '100%',
                                height: '100%'
                            },
                            legend: 'none',
                            tooltip: { trigger: 'none' },
                            slices: slices,
                            width: width,
                            height: height
                        };

                        var chart = new google.visualization.PieChart(document.getElementById('ccm-block-desktop-site-activity-chart'));
                        chart.draw(data, options);

                    }


                    $(window).resize(function() {
                        var $legend = $('#ccm-block-desktop-site-activity-legend');
                        $legend.show();
                        $legend.css("left", 20 + (width - $legend.width()) / 2);
                        $legend.css("top", (height - $legend.height()) / 2);
                    }).trigger('resize');

                });
            </script>

            <div id="ccm-block-desktop-site-activity-legend">
                <h6><?=t('Today')?></h6>
                <div class="ccm-block-desktop-site-activity-legend-row"
                     style="color: #00d0f8">
                    <?=t2('%s Form', '%s Forms', intval($formResults))?>
                </div>
                <div class="ccm-block-desktop-site-activity-legend-row"
                     style="color: #7c33b1">
                    <?=t2('%s Vote', '%s Votes', intval($surveyResults))?>
                </div>
                <div class="ccm-block-desktop-site-activity-legend-row"
                     style="color: #fed24b">
                    <?=t2('%s User', '%s Users', intval($signups))?>
                </div>
                <div class="ccm-block-desktop-site-activity-legend-row"
                     style="color: #00cc66">
                    <?=t2('%s Message', '%s Messages', intval($messages))?>
                </div>

            </div>

           <div id="ccm-block-desktop-site-activity-chart"></div>


        <?php } ?>

    <?php } ?>
</div>