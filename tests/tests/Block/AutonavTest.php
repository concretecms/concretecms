<?php
require(realpath(dirname(__FILE__) . '/../BlockTypeTestCase.php'));
class AutonavTest extends BlockTypeTestcase {
	
	protected $btHandle = 'autonav';
	protected $requestData = array(
		'empty' => array(),
		'all' => array(
			'orderBy' => 'display_asc',
			'displayPages' => 'top',
			'displayPagesCID' => 1,
			'displaySubPages' => 'all',
			'displaySubPageLevels' => 'all',
			'displaySubPageLevels' => 'all'
		)
	);

	protected $expectedRecordData = array(
		'empty' => array(
			'orderBy' => 'alpha_asc',
			'bID' => 1,
			'displaySubPages' => 'none'
		),
		'all' => array(
			'orderBy' => 'display_asc',
			'displayPages' => 'top',
			'displayPagesCID' => 1,
			'displayPagesIncludeSelf' => 0,
			'displaySubPages' => 'all',
			'displaySubPageLevels' => 'all',
			'displaySubPageLevels' => 'all'
		)
	);

	protected $expectedOutput = array(
		'empty' => '<ul class="nav"></ul>'
	);
}