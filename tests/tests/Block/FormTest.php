<?php

require_once 'BlockTypeTestCase.php';
class FormTest extends BlockTypeTestCase
{
    protected $btHandle = 'form';

    public function tearDown()
    {
        parent::tearDown();
        $db = Database::get();
        $db->Execute('drop table if exists btForm');
    }

    protected $requestData = array(
        'basic' => array(
            'recipientEmail' => 'testuser@concrete5.org',
        ),
    );

    protected $expectedRecordData = array(
        'basic' => array(
            'bID' => 1,
            'displayCaptcha' => false,
            'recipientEmail' => 'testuser@concrete5.org',
        ),
    );
}
