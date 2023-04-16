<?php

namespace Concrete\StartingPointPackage\OxfordShirt;

use Concrete\Core\Backup\ContentImporter;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\File\File;
use Concrete\Core\File\Filesystem;
use Concrete\Core\Package\FeaturedStartingPointPackageInterface;
use Concrete\Core\Package\StartingPointPackage;
use Concrete\Core\Tree\Node\Type\FileFolder;

class Controller extends StartingPointPackage implements FeaturedStartingPointPackageInterface
{
    protected $pkgHandle = 'oxford_shirt';
    protected $pkgContentProvidesFileThumbnails = false;

    public function getPackageName()
    {
        return t('Oxford Shirt');
    }

    public function getStartingPointThumbnail(): string
    {
        return ASSETS_URL . '/' . DIRNAME_THEMES . '/oxford_shirt/thumbnail.png';
    }

    public function getStartingPointDescriptionLines(): array
    {
        return [
            t('Intranets'),
            t('Portals'),
            t('Communication Hubs'),
            t('Corporate Blogs'),
            t('General purpose websites'),
        ];
    }

    public function getPackageDescription()
    {
        return t('Creates a full services portal site using the new Oxford Shirt theme.');
    }

    public function install_file_manager()
    {
        parent::install_file_manager();
        // Create documents node in file manager
        $filesystem = new Filesystem();
        $root = $filesystem->getRootFolder();
        $brand = $filesystem->addFolder($root, 'Brand');
        $collaboration = $filesystem->addFolder($root, 'Image Slider');
        $documents = $filesystem->addFolder($root, 'Documents');
        $gallery = $filesystem->addFolder($root, 'Gallery');
        $heroes = $filesystem->addFolder($root, 'Hero Images');
        $team = $filesystem->addFolder($root, 'Team');
        $video = $filesystem->addFolder($root, 'Video');
    }

    public function install_config()
    {
        parent::install_config();
        $themePaths = [
            '/account' => 'oxford_shirt',
            '/members/profile' => ['oxford_shirt', 'profile.php'],
        ];
        $config = $this->app->make('config');
        $config->save('app.theme_paths', $themePaths);
    }

    public function import_files()
    {
        parent::import_files();

        $importer = new ContentImporter();

        // Now move the files
        $importer->moveFilesByName(['oxford_shirt_logo_light.svg', 'oxford_shirt_logo_dark.svg'], 'Brand');
        $importer->moveFilesByName(['collaboration-01.jpg', 'collaboration-02.jpg', 'collaboration-03.jpg'], 'Image Slider');
        $importer->moveFilesByName(['dummy.pdf'], 'Documents');
        $importer->moveFilesByName(['gallery-headphones.jpg', 'gallery-shoes.jpg', 'gallery-shoes2.jpg', 'gallery-skincare.jpg', 'gallery-watch.jpg', 'gallery-watch2.jpg', 'oil_paint.png', 'abstract.jpg', 'Rectangle_84.jpg', 'Rectangle_85.jpg'], 'Gallery');
        $importer->moveFilesByName(['red_sculpture.jpg', 'corner_building.jpg'], 'Hero Images');
        $importer->moveFilesByName(['testimonial-large.jpg', 'malikah_haney.png'], 'Team');
        $importer->moveFilesByName(['video-thumbnail.jpg', 'atomik-documentation-video.mp4'], 'Video');

    }
}
