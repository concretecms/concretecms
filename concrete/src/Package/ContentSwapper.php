<?php
namespace Concrete\Core\Package;


use Concrete\Core\Backup\ContentImporter;
use Concrete\Core\File\FileList;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\PageList;
use Concrete\Core\Page\Stack\StackList;
use Concrete\Core\Page\Type\Type;

class ContentSwapper implements ContentSwapperInterface
{

    public function allowsFullContentSwap(Package $package)
    {
        return $package->allowsFullContentSwap();
    }

    protected function validateClearSiteContents($options)
    {
        if (\Core::make('app')->isRunThroughCommandLineInterface()) {
            $result = true;
        } else {
            $result = false;
            $u = new \User();
            if ($u->isSuperUser()) {
                // this can ONLY be used through the post. We will use the token to ensure that
                $valt = \Core::make('helper/validation/token');
                if ($valt->validate('install_options_selected', $options['ccm_token'])) {
                    $result = true;
                }
            }
        }

        return $result;
    }

    /**
     * Removes any existing pages, files, stacks, block and page types and installs content from the package.
     *
     * @param $options
     */
    public function swapContent(Package $package, $options)
    {
        if ($this->validateClearSiteContents($options)) {
            \Core::make('cache/request')->disable();

            $pl = new PageList();
            $pages = $pl->getResults();
            foreach ($pages as $c) {
                $c->delete();
            }

            $fl = new FileList();
            $files = $fl->getResults();
            foreach ($files as $f) {
                $f->delete();
            }

            // clear stacks
            $sl = new StackList();
            foreach ($sl->get() as $c) {
                $c->delete();
            }

            $home = \Page::getByID(HOME_CID);
            $blocks = $home->getBlocks();
            foreach ($blocks as $b) {
                $b->deleteBlock();
            }

            $pageTypes = Type::getList();
            foreach ($pageTypes as $ct) {
                $ct->delete();
            }

            // Set the page type of the home page to 0, because
            // if it has a type the type will be gone since we just
            // deleted it
            $home = Page::getByID(HOME_CID);
            $home->setPageType(null);

            // now we add in any files that this package has
            if (is_dir($package->getPackagePath() . '/content_files')) {
                $ch = new ContentImporter();
                $computeThumbnails = true;
                if ($package->contentProvidesFileThumbnails()) {
                    $computeThumbnails = false;
                }
                $ch->importFiles($package->getPackagePath() . '/content_files', $computeThumbnails);
            }

            // now we parse the content.xml if it exists.

            $ci = new ContentImporter();
            $ci->importContentFile($package->getPackagePath() . '/content.xml');

            \Core::make('cache/request')->enable();
        }
    }


}
