<?php

namespace Concrete\Block\File;

use Concrete\Core\Block\BlockController;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Feature\Features;
use Concrete\Core\Feature\UsesFeatureInterface;
use Concrete\Core\File\File;
use Concrete\Core\File\Tracker\FileTrackableInterface;

class Controller extends BlockController implements FileTrackableInterface, UsesFeatureInterface
{
    /**
     * @var int
     */
    protected $btInterfaceWidth = 300;

    /**
     * @var bool
     */
    protected $btCacheBlockOutput = true;

    /**
     * @var bool
     */
    protected $btCacheBlockOutputOnPost = true;

    /**
     * @var bool
     */
    protected $btCacheBlockOutputForRegisteredUsers = true;

    /**
     * @var int
     */
    protected $btInterfaceHeight = 320;

    /**
     * @var string
     */
    protected $btTable = 'btContentFile';

    /**
     * @var string[]
     */
    protected $btExportFileColumns = ['fID'];

    /**
     * @var string
     */
    protected $fileLinkText;

    /**
     * @var int|null
     */
    protected $fID;

    /**
     * Used for localization. If we want to localize the name/description we have to include this.
     *
     * @return string
     */
    public function getBlockTypeDescription()
    {
        return t('Link to files stored in the asset library.');
    }

    /**
     * @return string
     */
    public function getBlockTypeName()
    {
        return t('File');
    }

    /**
     * @return array<string, string>
     */
    public function getJavaScriptStrings()
    {
        return ['file-required' => t('You must select a file.')];
    }

    /**
     * @return string[]
     */
    public function getRequiredFeatures(): array
    {
        return [
            Features::BASICS,
        ];
    }

    /**
     * @return string|null
     */
    public function getSearchableContent()
    {
        return $this->fileLinkText;
    }

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     *
     * @return void
     */
    public function add()
    {
        $this->set('bf', null);
        $this->set('fileLinkText', null);
        $this->set('forceDownload', 0);

        $this->set('al', $this->app->make('helper/concrete/file_manager'));
    }

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     *
     * @return void
     */
    public function edit()
    {
        $this->set('al', $this->app->make('helper/concrete/file_manager'));
        $this->set('bf', $this->getFileObject());
        $this->set('fileLinkText', $this->getLinkText());
    }

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     *
     * @return void
     */
    public function composer()
    {
        $this->edit();
    }

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     *
     * @return ErrorList
     */
    public function validate_composer()
    {
        $f = $this->getFileObject();
        /** @var ErrorList $e */
        $e = $this->app->make('helper/validation/error');
        if (!is_object($f) || !$f->getFileID()) {
            $e->add(t('You must specify a valid file.'));
        }

        return $e;
    }

    /**
     * @return bool
     */
    public function isComposerControlDraftValueEmpty()
    {
        $f = $this->getFileObject();
        if (is_object($f)) {
            return false;
        }

        return true;
    }

    /**
     * @param array<string, mixed> $args
     *
     * @return void
     */
    public function save($args)
    {
        if (is_array($args) && isset($args['forceDownload'])) {
            $args['forceDownload'] = ($args['forceDownload']) ? '1' : '0';
        }

        parent::save($args);
    }

    /**
     * @param array<string, mixed> $args
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     *
     * @return ErrorList
     */
    public function validate($args)
    {
        /** @var ErrorList $e */
        $e = $this->app->make('helper/validation/error');
        if ($args['fID'] < 1) {
            $e->add(t('You must select a file.'));
        }
        if (trim($args['fileLinkText']) == '') {
            $e->add(t('You must enter the link text.'));
        }

        return $e;
    }

    /**
     * @return int|null
     */
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

    /**
     * @return string|null
     */
    public function getLinkText()
    {
        if ($this->fileLinkText) {
            return $this->fileLinkText;
        }
        $f = $this->getFileObject();
        /** @phpstan-ignore-next-line */
        return $f->getTitle();
    }

    /**
     * @return int[]
     */
    public function getUsedFiles()
    {
        return [$this->fID];
    }
}
