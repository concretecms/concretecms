<?php

defined('C5_EXECUTE') or die("Access Denied.");

$ch = Page::getByPath("/dashboard");
$chp = new Permissions($ch);
if (!$chp->canRead()) {
	die(t("Access Denied."));
}

Loader::library('3rdparty/open_flash_chart' );
Loader::model('page_statistics');
$daysRow = array();
// first, we grab the last 5 days
$viewsArray = array();
$u = new User();
$max = 0;

for ($i = -4; $i < 1; $i++) {
	$date = date('Y-m-d', strtotime($i . ' days'));
	if ($i == 0) {
		$daysRow[] = t('Today');
	} else {
		$daysRow[] = strftime('%a', strtotime($i . ' days'));
	}
	$total = PageStatistics::getTotalPageViewsForOthers($u, $date);
	$viewsArray[] = $total;
	if ($total > $max) {
		$max = $total;
	}
}

$g = new graph();
$g->set_title('&nbsp;', '{color: #ffffff}');

$g->set_data($viewsArray);
$g->bg_colour = '#ffffff';
$g->set_inner_background('#ffffff', "#cccccc", 90);
// we add the 3 line types and key labels
$g->line_dot( 3, 5, '#4C85BB', false, 10);

$g->set_x_labels( $daysRow );
$g->set_x_label_style( 10, '#ababab', 0, 2 );

$g->x_axis_colour( '#333333', '#bebebe' );
$g->y_axis_colour( '#333333', '#bebebe' );

$g->set_y_max( $max );
$g->num_decimals = 0;
$g->is_fixed_num_decimals_forced = true;
$g->y_label_steps( 5 );
$g->set_y_legend( t('Views'), 12, '#333333' );

echo $g->render();