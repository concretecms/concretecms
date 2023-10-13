<?php
namespace Concrete\Core\Install\StartingPoint\Installer\Routine\Base;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Entity\File\Image\Thumbnail\Type\Type as ThumbnailType;
use Concrete\Core\File\Filesystem;
use Concrete\Core\File\StorageLocation\StorageLocation;
use Concrete\Core\File\StorageLocation\Type\Type;
use Concrete\Core\User\Group\GroupRepository;

class InstallFileManagerSupportRoutineHandler
{

    /**
     * @var Repository
     */
    protected $config;

    /**
     * @var GroupRepository
     */
    protected $groupRepository;

    public function __construct(GroupRepository $groupRepository, Repository $config)
    {
        $this->groupRepository = $groupRepository;
        $this->config = $config;
    }

    public function __invoke()
    {
        $type = Type::add('default', t('Default'));
        Type::add('local', t('Local'));
        $configuration = $type->getConfigurationObject();
        $fsl = StorageLocation::add($configuration, t('Default'), true);

        $filesystem = new Filesystem();
        $tree = $filesystem->create();
        $filesystem->setDefaultPermissions($tree);

        $thumbnailType = new ThumbnailType();
        $thumbnailType->requireType();
        $thumbnailType->setName(tc('ThumbnailTypeName', 'File Manager Thumbnails'));
        $thumbnailType->setHandle($this->config->get('concrete.icons.file_manager_listing.handle'));
        $thumbnailType->setSizingMode($thumbnailType::RESIZE_EXACT);
        $thumbnailType->setIsUpscalingEnabled(true);
        $thumbnailType->setWidth($this->config->get('concrete.icons.file_manager_listing.width'));
        $thumbnailType->setHeight($this->config->get('concrete.icons.file_manager_listing.height'));
        $thumbnailType->save();

        $thumbnailType = new ThumbnailType();
        $thumbnailType->requireType();
        $thumbnailType->setName(tc('ThumbnailTypeName', 'File Manager Detail Thumbnails'));
        $thumbnailType->setHandle($this->config->get('concrete.icons.file_manager_detail.handle'));
        $thumbnailType->setSizingMode($thumbnailType::RESIZE_EXACT);
        $thumbnailType->setIsUpscalingEnabled(false);
        $thumbnailType->setWidth($this->config->get('concrete.icons.file_manager_detail.width'));
        $thumbnailType->setHeight($this->config->get('concrete.icons.file_manager_detail.height'));
        $thumbnailType->save();

        $g1 = $this->groupRepository->getGroupById(GUEST_GROUP_ID);
        $g3 = $this->groupRepository->getGroupById(ADMIN_GROUP_ID);
        $filesystem = new Filesystem();
        $folder = $filesystem->getRootFolder();
        $folder->assignPermissions($g1, ['view_file_folder_file']);
        $folder->assignPermissions(
            $g3,
            [
                'view_file_folder_file',
                'search_file_folder',
                'edit_file_folder',
                'edit_file_folder_file_properties',
                'edit_file_folder_file_contents',
                'copy_file_folder_files',
                'edit_file_folder_permissions',
                'delete_file_folder_files',
                'delete_file_folder',
                'add_file',
            ]
        );
    }


}
