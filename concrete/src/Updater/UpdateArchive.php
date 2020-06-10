<?php

namespace Concrete\Core\Updater;

class UpdateArchive extends Archive
{
    public function __construct()
    {
        parent::__construct();
        $this->targetDirectory = DIR_CORE_UPDATES;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Updater\Archive::install()
     */
    public function install($file, $inplace = true)
    {
        parent::install($file, $inplace);
    }
}
