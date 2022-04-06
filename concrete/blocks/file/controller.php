<?php

namespace Concrete\Block\File;

use Concrete\Core\Block\BlockController;
use Concrete\Core\Feature\Features;
use Concrete\Core\Feature\UsesFeatureInterface;
use Concrete\Core\File\File;
use Concrete\Core\File\Tracker\FileTrackableInterface;

class Controller extends BlockController implements FileTrackableInterface, UsesFeatureInterface
{
    protected $btInterfaceWidth = 300;

    protected $btCacheBlockRecord = true;

    protected $btCacheBlockOutput = true;

    protected $btCacheBlockOutputOnPost = true;

    protected $btCacheBlockOutputForRegisteredUsers = true;

    protected $btInterfaceHeight = 320;

    protected $btTable = 'btContentFile';

    protected $btExportFileColumns = ['fID'];

    /**
     * @var string
     */
    public $fileLinkText;

    /**
     * @var int|null
     */
    public $fID;

    /**
     * @var boolean|null
     */
    public $forceDownload;

    /**
     * @var boolean|null
     */
    public $filePassword;

    /**
     * Used for localization. If we want to localize the name/description we have to include this.
     */
    public function getBlockTypeDescription()
    {
        return t('Link to files stored in the asset library.');
    }

    public function getBlockTypeName()
    {
        return t('File');
    }

    public function getRequiredFeatures(): array
    {
        return [
            Features::BASICS,
        ];
    }

    public function getSearchableContent()
    {
        return $this->fileLinkText;
    }

    public function add()
    {
        $this->set('bf', null);
        $this->set('fileLinkText', null);
        $this->set('forceDownload', 0);

        $this->set('al', $this->app->make('helper/concrete/file_manager'));
    }

    public function edit()
    {
        $this->set('al', $this->app->make('helper/concrete/file_manager'));
        $this->set('bf', $this->getFileObject());
        $this->set('fileLinkText', $this->getLinkText());
    }

    public function composer()
    {
        $this->edit();
    }

    public function validate_composer()
    {
        $f = $this->getFileObject();
        $e = $this->app->make('helper/validation/error');
        if (!is_object($f) || !$f->getFileID()) {
            $e->add(t('You must specify a valid file.'));
        }

        return $e;
    }

    public function isComposerControlDraftValueEmpty()
    {
        $f = $this->getFileObject();
        if (is_object($f)) {
            return false;
        }

        return true;
    }

    public function save($args)
    {
        if (is_array($args) && isset($args['forceDownload'])) {
            $args['forceDownload'] = ($args['forceDownload']) ? '1' : '0';
        }

        parent::save($args);
    }

    public function validate($args)
    {
        $e = $this->app->make('helper/validation/error');
        if ($args['fID'] < 1) {
            $e->add(t('You must select a file.'));
        }
        if (trim($args['fileLinkText']) == '') {
            $e->add(t('You must enter the link text.'));
        }

        return $e;
    }

    public function getFileID()
    {
        return $this->fID;
    }

    /**
     * @return \Concrete\Core\Entity\File\File|null
     */
    public function getFileObject()
    {
        if ($this->fID) {
            return File::getByID($this->fID);
        }

        return null;
    }

    public function getLinkText()
    {
        if ($this->fileLinkText) {
            return $this->fileLinkText;
        }
        $f = $this->getFileObject();
        if ($f) {
            return $f->getTitle();
        }
    }

    public function getUsedFiles()
    {
        return [$this->fID];
    }
}
