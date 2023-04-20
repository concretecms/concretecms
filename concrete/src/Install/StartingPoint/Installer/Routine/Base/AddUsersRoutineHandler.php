<?php

namespace Concrete\Core\Install\StartingPoint\Installer\Routine\Base;

use Concrete\Core\Application\Application;
use Concrete\Core\Authentication\AuthenticationType;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Install\StartingPoint\Installer\Routine\InstallOptionsAwareInterface;
use Concrete\Core\Install\StartingPoint\Installer\Routine\Traits\InstallOptionsAwareTrait;
use Concrete\Core\Tree\Node\NodeType;
use Concrete\Core\Tree\TreeType;
use Concrete\Core\Tree\Type\Group as GroupTree;
use Concrete\Core\User\Group\Command\AddGroupCommand;
use Concrete\Core\User\Group\FolderManager;
use Concrete\Core\User\RegistrationService;

class AddUsersRoutineHandler implements InstallOptionsAwareInterface
{

    use InstallOptionsAwareTrait;

    /**
     * @var Connection
     */
    protected $db;

    /**
     * @var Application
     */
    protected $app;

    /**
     * @var Repository
     */
    protected $config;

    /**
     * @var RegistrationService
     */
    protected $userRegistration;

    public function __construct(
        RegistrationService $userRegistration,
        Connection $db,
        Application $app,
        Repository $config
    ) {
        $this->userRegistration = $userRegistration;
        $this->db = $db;
        $this->app = $app;
        $this->config = $config;
    }

    public function __invoke()
    {
        // Firstly, install the core authentication types
        $cba = AuthenticationType::add('concrete', 'Standard');
        $coa = AuthenticationType::add('community', 'community.concretecms.com');
        $fba = AuthenticationType::add('facebook', 'Facebook');
        $gat = AuthenticationType::add('google', 'Google');
        $ext = AuthenticationType::add('external_concrete', 'External Concrete Site');

        $fba->disable();
        $coa->disable();
        $gat->disable();
        $ext->disable();

        TreeType::add('group');
        NodeType::add('group');
        $tree = GroupTree::get();
        $tree = GroupTree::add();

        // insert the default groups
        // create the groups our site users
        // specify the ID's since auto increment may not always be +1
        $command = new AddGroupCommand();
        $this->app->executeCommand(
            $command
                ->setName(tc('GroupName', 'Guest'))
                ->setDescription(
                    tc('GroupDescription', 'The guest group represents unregistered visitors to your site.')
                )
                ->setForcedNewGroupID(GUEST_GROUP_ID)
        );
        $this->app->executeCommand(
            $command
                ->setName(tc('GroupName', 'Registered Users'))
                ->setDescription(tc('GroupDescription', 'The registered users group represents all user accounts.'))
                ->setForcedNewGroupID(REGISTERED_GROUP_ID)
        );
        $this->app->executeCommand(
            $command
                ->setName(tc('GroupName', 'Administrators'))
                ->setDescription('')
                ->setForcedNewGroupID(ADMIN_GROUP_ID)
        );

        $this->userRegistration->createSuperUser(
            $this->installOptions->getUserPasswordHash(),
            $this->installOptions->getUserEmail()
        );

        $folderManager = new FolderManager();
        $folderManager->create();

        // Add Group Type + Default Role and assign them to the groups
        $db = $this->db;
        $db->executeQuery(
            'insert into GroupTypes (gtID, gtName, gtDefaultRoleID) values (?,?, ?)',
            [DEFAULT_GROUP_TYPE_ID, t("Group"), DEFAULT_GROUP_ROLE_ID]
        );
        $db->executeQuery('insert into GroupRoles (grID, grName) values (?,?)', [DEFAULT_GROUP_ROLE_ID, t("Member")]);
        $db->executeQuery(
            'insert into GroupTypeSelectedRoles (gtID, grID) values (?,?)',
            [DEFAULT_GROUP_TYPE_ID, DEFAULT_GROUP_ROLE_ID]
        );
        $db->executeQuery(
            'update `Groups` set gtID = ?, gDefaultRoleID = ?',
            [DEFAULT_GROUP_TYPE_ID, DEFAULT_GROUP_ROLE_ID]
        );
        $db->executeQuery('update UserGroups set grID = ?', [DEFAULT_GROUP_ROLE_ID]);

        $config = $this->app->make(Repository::class);
        if (!$config->get('concrete.email.default.address')) {
            $config->set('concrete.email.default.address', $this->installOptions->getUserEmail());
            $config->save('concrete.email.default.address', $this->installOptions->getUserEmail());
        }
    }


}
