<?php

namespace Concrete\StartingPointPackage\AtomikFull;

use Concrete\Core\Backup\ContentImporter;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\File\File;
use Concrete\Core\File\Filesystem;
use Concrete\Core\Package\StartingPointPackage;
use Concrete\Core\Tree\Node\Type\FileFolder;

class Controller extends StartingPointPackage
{
    protected $pkgHandle = 'atomik_full';
    protected $pkgContentProvidesFileThumbnails = true;

    public function getPackageName()
    {
        return t('Full Site (Atomik)');
    }

    public function getPackageDescription()
    {
        return t('Creates a full services agency site using the new Atomik theme.');
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

    public function install_config()
    {
        parent::install_config();
        $themePaths = [
            '/account' => 'atomik',
            '/members/profile' => ['atomik', 'profile.php'],
        ];
        $config = $this->app->make('config');
        $config->save('app.theme_paths', $themePaths);
    }

    public function import_files()
    {
        parent::import_files();

        $importer = new ContentImporter();

        // Now move the files
        $importer->moveFilesByName(['atomik-logo-transparent.png', 'atomik-logo.png'], 'Brand');
        $importer->moveFilesByName(['blog-01.jpg', 'blog-02.jpg', 'blog-03.jpg', 'blog-04.jpg', 'blog-05.jpg', 'blog-06.jpg'], 'Blog');
        $importer->moveFilesByName(['collaboration-01.jpg', 'collaboration-02.jpg', 'collaboration-03.jpg'], 'Collaboration Slider');
        $importer->moveFilesByName(['dummy.pdf'], 'Documents');
        $importer->moveFilesByName(['gallery-headphones.jpg', 'gallery-shoes.jpg', 'gallery-shoes2.jpg', 'gallery-skincare.jpg', 'gallery-watch.jpg', 'gallery-watch2.jpg'], 'Gallery');
        $importer->moveFilesByName(['hands-01.jpg', 'laptops-01.jpg', 'laptops-02.jpg', 'people-01.jpg', 'testimonial-01.jpg', 'testimonial-bg.jpg'], 'Stripes');
        $importer->moveFilesByName(['hero-01.jpg', 'hero-resources.jpg'], 'Hero Images');
        $importer->moveFilesByName(['logo-01.png', 'logo-02.png', 'logo-03.png', 'logo-04.png'], 'Logo Slider');
        $importer->moveFilesByName(['team-01.jpg', 'team-02.jpg', 'team-03.jpg', 'team-04.jpg', 'team-05.jpg', 'team-06.jpg'], 'Team');

    }
}
