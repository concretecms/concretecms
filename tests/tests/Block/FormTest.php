<?php

namespace Concrete\Tests\Block;

use Concrete\TestHelpers\Block\BlockTypeTestCase;
use Database;

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
