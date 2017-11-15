<?php

require_once __DIR__ . '/../BlockTypeTestCase.php';

class FormTest extends BlockTypeTestCase
{
    protected $btHandle = 'form';

    protected $requestData = [
        'basic' => [
            'recipientEmail' => 'testuser@concrete5.org',
        ],
    ];

    protected $expectedRecordData = [
        'basic' => [
            'bID' => 1,
            'displayCaptcha' => false,
            'recipientEmail' => 'testuser@concrete5.org',
        ],
    ];

    public function tearDown()
    {
        parent::tearDown();
        $db = Database::get();
        $db->Execute('drop table if exists btForm');
    }
}
