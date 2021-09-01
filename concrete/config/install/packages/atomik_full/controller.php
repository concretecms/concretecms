<?php

namespace Concrete\StartingPointPackage\AtomikFull;

use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\File\File;
use Concrete\Core\File\Filesystem;
use Concrete\Core\Package\StartingPointPackage;
use Concrete\Core\Tree\Node\Type\FileFolder;

class controller extends StartingPointPackage
{
    protected $pkgHandle = 'atomik_full';

    public function getPackageName()
    {
        return t('Atomik Theme');
    }

    public function getPackageDescription()
    {
        return t('Creates a full services agency site using the new Atomik theme.');
    }

    private function moveFiles(array $fileNames, string $folderName)
    {
        $folder = FileFolder::getNodeByName($folderName);
        if ($folder) {
            $db = $this->app->make(Connection::class);
            foreach ($fileNames as $name) {
                $fID = $db->fetchOne('select fID from FileVersions where fvFilename = ?', [$name]);
                if ($fID) {
                    $file = File::getByID($fID);
                    if ($file) {
                        $fileNode = $file->getFileNodeObject();
                        if ($fileNode) {
                            $fileNode->move($folder);
                        }
                    }
                }
            }
        }
    }

    public function install_file_manager()
    {
        parent::install_file_manager();
        // Create documents node in file manager
        $filesystem = new Filesystem();
        $root = $filesystem->getRootFolder();
        $documents = $filesystem->addFolder($root, 'Documents');
        $brand = $filesystem->addFolder($root, 'Brand');
        $blog = $filesystem->addFolder($root, 'Blog');
        $gallery = $filesystem->addFolder($root, 'Gallery');
        $collaboration = $filesystem->addFolder($root, 'Collaboration Slider');
        $heroes = $filesystem->addFolder($root, 'Hero Images');
        $logoSlider = $filesystem->addFolder($root, 'Logo Slider');
        $stripes = $filesystem->addFolder($root, 'Stripes');
        $team = $filesystem->addFolder($root, 'Team');
    }

    public function import_files()
    {
        parent::import_files();

        // Now move the files
        $this->moveFiles(['atomik-logo-transparent.png', 'atomik-logo.png'], 'Brand');
        $this->moveFiles(['blog-01.jpg', 'blog-02.jpg', 'blog-03.jpg', 'blog-04.jpg', 'blog-05.jpg', 'blog-06.jpg'], 'Blog');
        $this->moveFiles(['collaboration-01.jpg', 'collaboration-02.jpg', 'collaboration-03.jpg'], 'Collaboration Slider');
        $this->moveFiles(['dummy.pdf'], 'Documents');
        $this->moveFiles(['gallery-headphones.jpg', 'gallery-shoes.jpg', 'gallery-shoes2.jpg', 'gallery-skincare.jpg', 'gallery-watch.jpg', 'gallery-watch2.jpg'], 'Gallery');
        $this->moveFiles(['hands-01.jpg', 'laptops-01.jpg', 'laptops-02.jpg', 'people-01.jpg', 'testimonial-01.jpg', 'testimonial-bg.jpg'], 'Stripes');
        $this->moveFiles(['hero-01.jpg', 'hero-resources.jpg'], 'Hero Images');
        $this->moveFiles(['logo-01.png', 'logo-02.png', 'logo-03.png', 'logo-04.png'], 'Logo Slider');
        $this->moveFiles(['team-01.jpg', 'team-02.jpg', 'team-03.jpg', 'team-04.jpg', 'team-05.jpg', 'team-06.jpg'], 'Team');

    }
}
