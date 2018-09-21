<?php

namespace Concrete\Tests\User;

use Concrete\Core\Conversation\Message\Author;
use Concrete\Core\Conversation\Message\AuthorFormatter;
use Concrete\Core\File\StorageLocation\StorageLocation;
use Concrete\Core\File\StorageLocation\Type\Type;
use Concrete\TestHelpers\User\UserTestCase;
use Core;

class UserTest extends UserTestCase
{
    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->metadatas[] = 'Concrete\Core\Entity\File\StorageLocation\StorageLocation';
        $this->metadatas[] = 'Concrete\Core\Entity\File\StorageLocation\Type\Type';
        $this->metadatas[] = 'Concrete\Core\Entity\Site\Site';
        $this->metadatas[] = 'Concrete\Core\Entity\Site\Locale';
        $this->metadatas[] = 'Concrete\Core\Entity\Site\Type';
        $this->metadatas[] = 'Concrete\Core\Entity\Site\Tree';
        $this->metadatas[] = 'Concrete\Core\Entity\Site\SiteTree';
    }

    public function tearDown()
    {
        parent::tearDown();
        $this->truncateTables();
    }

    public function testCreateLegacy()
    {
        $ui = \UserInfo::add([
            'uName' => 'andrew',
            'uEmail' => 'andrew@concrete5.org',
        ]);
        $this->assertEquals(1, $ui->getUserID());
        $this->assertEquals('andrew', $ui->getUserName());
        $this->assertEquals('andrew@concrete5.org', $ui->getUserEmail());

        $ui = \Concrete\Core\User\UserInfo::add([
            'uName' => 'andrew2',
            'uEmail' => 'andrew2@concrete5.org',
        ]);
        $this->assertEquals(2, $ui->getUserID());
        $this->assertEquals('andrew2', $ui->getUserName());
        $this->assertEquals('andrew2@concrete5.org', $ui->getUserEmail());
    }

    public function testRegisterLegacy()
    {
        $ui = \UserInfo::register([
            'uName' => 'andrew',
            'uEmail' => 'andrew@concrete5.org',
        ]);
        $this->assertEquals(1, $ui->getUserID());
        $this->assertEquals('andrew', $ui->getUserName());
        $this->assertEquals('andrew@concrete5.org', $ui->getUserEmail());
    }

    public function testCreateSuperUserLegacy()
    {
        $ui = \UserInfo::addSuperUser('test', 'andrew@concrete5.org');
        $this->assertEquals(USER_SUPER_ID, $ui->getUserID());
        $this->assertEquals(USER_SUPER, $ui->getUserName());
        $this->assertEquals('andrew@concrete5.org', $ui->getUserEmail());
    }

    public function testCreateNew()
    {
        $service = Core::make('user/registration');
        $ui = $service->create(['uName' => 'andrew', 'uEmail' => 'andrew@concrete5.org']);
        $this->assertEquals(1, $ui->getUserID());
        $this->assertEquals('andrew', $ui->getUserName());
        $this->assertEquals('andrew@concrete5.org', $ui->getUserEmail());
    }

    public function testGravatar()
    {
        $site = \Core::make('site')->installDefault();

        $site->getConfigRepository()->set('user.gravatar.enabled', true);

        $service = Core::make('user/registration');
        $ui = $service->create(['uName' => 'andrew', 'uEmail' => 'andrew@concrete5.org']);
        $this->assertFalse($ui->hasAvatar());

        // Note, this is a FALLBACK avatar. That's why hasAvatar is false but the avatar actually returns an image.
        $avatar = $ui->getUserAvatar();
        $this->assertInstanceOf('Concrete\Core\User\Avatar\Gravatar', $avatar);
        $this->assertEquals('//www.gravatar.com/avatar/90c2803fabd994378063e84dd9a3ed92?s=80&d=mm&r=g', $avatar->getPath());

        $site->getConfigRepository()->clear('user.gravatar.enabled');
    }

    public function testAvatar()
    {
        $site = \Core::make('site')->installDefault();
        $type = Type::add('default', t('Default'));
        $configuration = $type->getConfigurationObject();
        $fsl = StorageLocation::add($configuration, 'Default', true);

        $service = Core::make('user/registration');
        $ui = $service->create(['uName' => 'andrew', 'uEmail' => 'andrew@concrete5.org']);
        $this->assertFalse($ui->hasAvatar());

        $avatar = $ui->getUserAvatar();
        $this->assertInstanceOf('Concrete\Core\User\Avatar\EmptyAvatar', $avatar);
        $this->assertEquals('<img src="/path/to/server/concrete/images/avatar_none.png" alt="andrew" class="u-avatar">',
            $avatar->output());
        $this->assertEquals('/path/to/server/concrete/images/avatar_none.png', $avatar->getPath());

        $ui->update(['uHasAvatar' => true]);
        // This is lame, I know.
        $ui = Core::make('Concrete\Core\User\UserInfoRepository')->getByID(1);
        $this->assertTrue($ui->hasAvatar());
        $avatar = $ui->getUserAvatar();
        $this->assertEquals('http://www.dummyco.com/path/to/server/application/files/avatars/1.jpg',
            $avatar->getPath());
        $this->assertEquals(
            '<img src="http://www.dummyco.com/path/to/server/application/files/avatars/1.jpg" alt="andrew" class="u-avatar">',
            $avatar->output());

        $service = Core::make('user/avatar');
        $service->removeAvatar($ui);

        // I KNOW I KNOW This is lame
        $ui = Core::make('Concrete\Core\User\UserInfoRepository')->getByID(1);
        $this->assertFalse($ui->hasAvatar());
    }

    public function testEmptyAvatar()
    {
        $author = new Author();
        $author->setName('Andrew');

        $formatter = new AuthorFormatter($author);
        $avatar = $formatter->getAvatar();

        $this->assertEquals(
            '<img src="/path/to/server/concrete/images/avatar_none.png" alt="Andrew" class="u-avatar">',
            $avatar
        );
    }

    public function testPublicProfileLink()
    {
        $site = \Core::make('site')->installDefault();

        $site->getConfigRepository()->set('user.profiles_enabled', false);

        $service = Core::make('user/registration');
        $ui = $service->create(['uName' => 'andrew', 'uEmail' => 'andrew@concrete5.org']);
        $this->assertEquals(null, $ui->getUserPublicProfileUrl());

        $site->getConfigRepository()->set('user.profiles_enabled', true);
        $this->assertInstanceOf('Concrete\Core\Url\UrlInterface', $ui->getUserPublicProfileUrl());
        $url = (string) $ui->getUserPublicProfileUrl();
        $this->assertEquals('http://www.dummyco.com/path/to/server/index.php/members/profile/view/1', $url);

        $site->getConfigRepository()->clear('user.profiles_enabled');
    }
}
