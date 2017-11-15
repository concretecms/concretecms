<?php

namespace Concrete\Tests\Block;

use Concrete\TestHelpers\Block\BlockTypeTestCase;

class ContentTest extends BlockTypeTestCase
{
    protected $btHandle = 'content';

    protected $requestData = [
        'empty' => [],
        'lipsum' => [
            'content' => '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce ac scelerisque lorem. Praesent vulputate blandit sem, vitae eleifend leo cursus et. Cras ultricies justo vitae lacinia tempor. Mauris euismod sem dolor, id molestie ante placerat et. Duis rutrum feugiat sem, luctus bibendum mauris sodales sed. Donec feugiat, mauris sed cursus viverra, nibh purus fringilla quam, eget laoreet mauris ipsum quis purus. Phasellus faucibus enim in enim sagittis, id vulputate velit fringilla. Integer neque tortor, tristique sit amet quam at, ultrices mollis metus. Nullam quis cursus nibh. Morbi dolor eros, euismod at adipiscing sit amet, suscipit sed urna. Nulla facilisi. Suspendisse porttitor, lacus quis feugiat varius, magna libero malesuada risus, sed tristique mauris risus vel erat. Fusce fringilla metus non massa cursus, sit amet ultricies purus consectetur. Integer cursus dui in neque cursus iaculis. Proin porta magna est, et lobortis arcu bibendum in.</p><p>Etiam posuere nec mauris sed tempor. Cras ut ipsum ac dolor auctor viverra adipiscing quis neque. Sed libero augue, congue quis fermentum vel, dictum eu lacus. Pellentesque sed mi et nisl vulputate interdum. Sed fermentum erat nulla, sed bibendum eros vehicula eget. Etiam sapien nunc, posuere laoreet tincidunt et, venenatis eu sapien. Vestibulum quis pellentesque urna. Vivamus in sapien in augue convallis elementum eget eget metus. Nunc laoreet, libero non dignissim egestas, risus diam ultrices tortor, nec commodo nibh nibh sed tellus. Donec nunc purus, commodo id lorem eget, luctus accumsan nisi. Nulla hendrerit tempor arcu, porta malesuada neque. Proin eget arcu ipsum. Nulla auctor pellentesque elit et pretium. Nam sit amet elit consequat, aliquam leo vulputate, vehicula eros. Aliquam laoreet erat at varius consequat. Ut feugiat posuere mauris, ut cursus sapien tincidunt a.</p>',
        ],
    ];

    protected $expectedRecordData = [
        'empty' => [
            'content' => '',
        ],
        'lipsum' => [
            'content' => '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce ac scelerisque lorem. Praesent vulputate blandit sem, vitae eleifend leo cursus et. Cras ultricies justo vitae lacinia tempor. Mauris euismod sem dolor, id molestie ante placerat et. Duis rutrum feugiat sem, luctus bibendum mauris sodales sed. Donec feugiat, mauris sed cursus viverra, nibh purus fringilla quam, eget laoreet mauris ipsum quis purus. Phasellus faucibus enim in enim sagittis, id vulputate velit fringilla. Integer neque tortor, tristique sit amet quam at, ultrices mollis metus. Nullam quis cursus nibh. Morbi dolor eros, euismod at adipiscing sit amet, suscipit sed urna. Nulla facilisi. Suspendisse porttitor, lacus quis feugiat varius, magna libero malesuada risus, sed tristique mauris risus vel erat. Fusce fringilla metus non massa cursus, sit amet ultricies purus consectetur. Integer cursus dui in neque cursus iaculis. Proin porta magna est, et lobortis arcu bibendum in.</p><p>Etiam posuere nec mauris sed tempor. Cras ut ipsum ac dolor auctor viverra adipiscing quis neque. Sed libero augue, congue quis fermentum vel, dictum eu lacus. Pellentesque sed mi et nisl vulputate interdum. Sed fermentum erat nulla, sed bibendum eros vehicula eget. Etiam sapien nunc, posuere laoreet tincidunt et, venenatis eu sapien. Vestibulum quis pellentesque urna. Vivamus in sapien in augue convallis elementum eget eget metus. Nunc laoreet, libero non dignissim egestas, risus diam ultrices tortor, nec commodo nibh nibh sed tellus. Donec nunc purus, commodo id lorem eget, luctus accumsan nisi. Nulla hendrerit tempor arcu, porta malesuada neque. Proin eget arcu ipsum. Nulla auctor pellentesque elit et pretium. Nam sit amet elit consequat, aliquam leo vulputate, vehicula eros. Aliquam laoreet erat at varius consequat. Ut feugiat posuere mauris, ut cursus sapien tincidunt a.</p>',
        ],
    ];

    protected $expectedOutput = [
        'lipsum' => '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce ac scelerisque lorem. Praesent vulputate blandit sem, vitae eleifend leo cursus et. Cras ultricies justo vitae lacinia tempor. Mauris euismod sem dolor, id molestie ante placerat et. Duis rutrum feugiat sem, luctus bibendum mauris sodales sed. Donec feugiat, mauris sed cursus viverra, nibh purus fringilla quam, eget laoreet mauris ipsum quis purus. Phasellus faucibus enim in enim sagittis, id vulputate velit fringilla. Integer neque tortor, tristique sit amet quam at, ultrices mollis metus. Nullam quis cursus nibh. Morbi dolor eros, euismod at adipiscing sit amet, suscipit sed urna. Nulla facilisi. Suspendisse porttitor, lacus quis feugiat varius, magna libero malesuada risus, sed tristique mauris risus vel erat. Fusce fringilla metus non massa cursus, sit amet ultricies purus consectetur. Integer cursus dui in neque cursus iaculis. Proin porta magna est, et lobortis arcu bibendum in.</p><p>Etiam posuere nec mauris sed tempor. Cras ut ipsum ac dolor auctor viverra adipiscing quis neque. Sed libero augue, congue quis fermentum vel, dictum eu lacus. Pellentesque sed mi et nisl vulputate interdum. Sed fermentum erat nulla, sed bibendum eros vehicula eget. Etiam sapien nunc, posuere laoreet tincidunt et, venenatis eu sapien. Vestibulum quis pellentesque urna. Vivamus in sapien in augue convallis elementum eget eget metus. Nunc laoreet, libero non dignissim egestas, risus diam ultrices tortor, nec commodo nibh nibh sed tellus. Donec nunc purus, commodo id lorem eget, luctus accumsan nisi. Nulla hendrerit tempor arcu, porta malesuada neque. Proin eget arcu ipsum. Nulla auctor pellentesque elit et pretium. Nam sit amet elit consequat, aliquam leo vulputate, vehicula eros. Aliquam laoreet erat at varius consequat. Ut feugiat posuere mauris, ut cursus sapien tincidunt a.</p>',
    ];

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->tables[] = 'SystemContentEditorSnippets';
        $this->tables[] = 'btContentLocal';
    }
}
