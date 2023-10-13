<?php
namespace Concrete\Core\Install\StartingPoint\Installer\Routine\Base;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\File\Service\File;

class CreateDirectoriesRoutineHandler
{

    /**
     * @var File
     */
    protected $file;

    /**
     * @var Repository
     */
    protected $config;

    public function __construct(File $file, Repository $config)
    {
        $this->file = $file;
        $this->config = $config;
    }

    public function __invoke()
    {
        // Delete generated overrides and doctrine
        if (is_dir(DIR_CONFIG_SITE . '/generated_overrides')) {
            $this->file->removeAll(DIR_CONFIG_SITE . '/generated_overrides');
        }
        if (is_dir($this->config->get('database.proxy_classes'))) {
            $this->file->removeAll($this->config->get('database.proxy_classes'));
        }

        if (!is_dir($this->config->get('concrete.cache.directory'))) {
            mkdir($this->config->get('concrete.cache.directory'), $this->config->get('concrete.filesystem.permissions.directory'));
            chmod($this->config->get('concrete.cache.directory'), $this->config->get('concrete.filesystem.permissions.directory'));
        }

        if (!is_dir(DIR_FILES_UPLOADED_STANDARD . REL_DIR_FILES_INCOMING)) {
            mkdir(
                DIR_FILES_UPLOADED_STANDARD . REL_DIR_FILES_INCOMING,
                $this->config->get('concrete.filesystem.permissions.directory'));
            chmod(
                DIR_FILES_UPLOADED_STANDARD . REL_DIR_FILES_INCOMING,
                $this->config->get('concrete.filesystem.permissions.directory'));
        }
    }


}
