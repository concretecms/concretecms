<?php

namespace Concrete\Tests\Block;

use Concrete\TestHelpers\Block\BlockTypeTestCase;

class AutonavTest extends BlockTypeTestCase
{
    protected $btHandle = 'autonav';
    protected $requestData = [
        'empty' => [],
        'all' => [
            'orderBy' => 'display_asc',
            'displayPages' => 'top',
            'displayPagesCID' => 1,
            'displaySubPages' => 'all',
            'displaySubPageLevels' => 'all',
            'displaySubPageLevels' => 'all',
        ],
    ];

    protected $expectedRecordData = [
        'empty' => [
            'orderBy' => 'alpha_asc',
            'bID' => 1,
            'displaySubPages' => 'none',
        ],
        'all' => [
            'orderBy' => 'display_asc',
            'displayPages' => 'top',
            'displayPagesCID' => 1,
            'displayPagesIncludeSelf' => 0,
            'displaySubPages' => 'all',
            'displaySubPageLevels' => 'all',
            'displaySubPageLevels' => 'all',
        ],
    ];

    protected $expectedOutput = [
        'empty' => '',
    ];
}
