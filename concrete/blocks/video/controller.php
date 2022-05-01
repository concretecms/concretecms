<?php

namespace Concrete\Block\Video;

use Concrete\Core\Block\BlockController;
use Concrete\Core\Feature\Features;
use Concrete\Core\Feature\UsesFeatureInterface;
use Concrete\Core\File\File;

class Controller extends BlockController implements UsesFeatureInterface
{
    /**
     * @var int
     */
    protected $btInterfaceWidth = 450;

    /**
     * @var int
     */
    protected $btInterfaceHeight = 440;

    /**
     * @var string
     */
    protected $btTable = 'btVideo';

    /**
     * @var bool
     */
    protected $btCacheBlockOutput = true;

    /**
     * @var bool
     */
    protected $btCacheBlockOutputOnPost = true;

    /**
     * @var string[]
     */
    protected $btExportFileColumns = ['webmfID', 'oggfID', 'posterfID', 'mp4fID'];

    /**
     * @var string
     */
    protected $btWrapperClass = 'ccm-ui';

    /**
     * @var int|null
     */
    protected $mp4fID;

    /**
     * @var int|null
     */
    protected $webmfID;

    /**
     * @var int|null
     */
    protected $oggfID;

    /**
     * @var int|null
     */
    protected $posterfID;

    /**
     * @var int|null
     */
    protected $videoSize;

    /**
     * @var int|null
     */
    protected $width;

    /**
     * @var string[]
     */
    protected $helpers = ['form'];

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getBlockTypeDescription()
    {
        return t('Embeds uploaded video into a web page. Supports WebM, Ogg, and Quicktime/MPEG4 formats.');
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getBlockTypeName()
    {
        return t('Video Player');
    }

    /**
     * Get the file id for the mp4 file.
     *
     * @return int
     */
    public function getMp4FileID(): int
    {
        return $this->mp4fID ?? 0;
    }

    /**
     * Get the file id for the webm file.
     *
     * @return int
     */
    public function getWebmFileID(): int
    {
        return $this->webmfID ?? 0;
    }

    /**
     * Get the file id for the webm file.
     *
     * @return int
     */
    public function getOggFileID(): int
    {
        return $this->oggfID ?? 0;
    }

    public function getPosterFileID(): int
    {
        return $this->posterfID ?? 0;
    }

    public function getMp4FileObject(): ?\Concrete\Core\Entity\File\File
    {
        return File::getByID($this->getMp4FileID());
    }

    public function getOggFileObject(): ?\Concrete\Core\Entity\File\File
    {
        return File::getByID($this->getOggFileID());
    }

    public function getWebmFileObject(): ?\Concrete\Core\Entity\File\File
    {
        return File::getByID($this->getWebmFileID());
    }

    public function getPosterFileObject(): ?\Concrete\Core\Entity\File\File
    {
        return File::getByID($this->getPosterFileID());
    }

    /**
     * {@inheritdoc}
     *
     * @return string[]
     */
    public function getRequiredFeatures(): array
    {
        return [
            Features::VIDEO,
        ];
    }

    /**
     * Run when a block is added or edited. Automatically saves block data against the block's database table. If a block needs to do more than this (save to multiple tables, upload files, etc... it should override this.
     *
     * @param array<string,mixed> $data
     *
     * @return void
     */
    public function save($data)
    {
        $data += [
            'webmfID' => 0,
            'oggfID' => 0,
            'mp4fID' => 0,
            'posterfID' => 0,
            'width' => 0,
            'videoSize' => 0,
            'title' => '',
        ];
        $args = [
            'webmfID' => max(0, (int) $data['webmfID']),
            'oggfID' => max(0, (int) $data['oggfID']),
            'mp4fID' => max(0, (int) $data['mp4fID']),
            'posterfID' => max(0, (int) $data['posterfID']),
            'videoSize' => max(0, (int) $data['videoSize']),
            'title' =>  $data['title'],
        ];
        $args['width'] = $args['videoSize'] === 0 || $args['videoSize'] == 1 ? 0 : (int) $data['width'];

        parent::save($args);
    }

    /**
     * The view function called when ever a block is viewed on a page.
     *
     * @return void
     */
    public function view()
    {
        $mp4File = $this->getMp4FileObject();
        $webmFile = $this->getWebmFileObject();
        $posterFile = $this->getPosterFileObject();
        $oggFile = $this->getOggFileObject();

        $this->set('posterURL', ($posterFile === null || $posterFile->getApprovedVersion() === null) ? '' : $posterFile->getApprovedVersion()->getURL());
        $this->set('mp4URL', ($mp4File === null || $mp4File->getApprovedVersion() === null) ? '' : $mp4File->getApprovedVersion()->getURL());
        $this->set('webmURL', ($webmFile === null || $webmFile->getApprovedVersion() === null) ? '' : $webmFile->getApprovedVersion()->getURL());
        $this->set('oggURL', ($oggFile === null || $oggFile->getApprovedVersion() === null) ? '' : $oggFile->getApprovedVersion()->getURL());
    }
}
