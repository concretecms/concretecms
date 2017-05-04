<?php
namespace Concrete\Block\Video;

use Concrete\Core\Block\BlockController;
use File;

class Controller extends BlockController
{
    protected $btInterfaceWidth = 450;
    protected $btInterfaceHeight = 440;
    protected $btTable = 'btVideo';
    protected $btCacheBlockOutput = true;
    protected $btCacheBlockOutputOnPost = true;
    protected $btExportFileColumns = ['fID'];
    protected $btWrapperClass = 'ccm-ui';

    public function getBlockTypeDescription()
    {
        return t('Embeds uploaded video into a web page. Supports WebM, Ogg, and Quicktime/MPEG4 formats.');
    }

    public function getBlockTypeName()
    {
        return t('Video Player');
    }

    public function getMp4FileID()
    {
        return isset($this->mp4fID) ? (int) $this->mp4fID : 0;
    }

    public function getWebmFileID()
    {
        return isset($this->webmfID) ? (int) $this->webmfID : 0;
    }

    public function getOggFileID()
    {
        return isset($this->oggfID) ? (int) $this->oggfID : 0;
    }

    public function getPosterFileID()
    {
        return isset($this->posterfID) ? (int) $this->posterfID : 0;
    }

    public function getMp4FileObject()
    {
        return ($id = $this->getMp4FileID()) ? File::getByID($id) : null;
    }

    public function getOggFileObject()
    {
        return ($id = $this->getOggFileID()) ? File::getByID($id) : null;
    }

    public function getWebmFileObject()
    {
        return ($id = $this->getWebmFileID()) ? File::getByID($id) : null;
    }

    public function getPosterFileObject()
    {
        return ($id = $this->getPosterFileID()) ? File::getByID($id) : null;
    }

    public function save($data)
    {
        $data += [
            'webmfID' => 0,
            'oggfID' => 0,
            'mp4fID' => 0,
            'posterfID' => 0,
            'width' => 0,
            'videoSize' => 0,
        ];
        $args = [
            'webmfID' => max(0, (int) $data['webmfID']),
            'oggfID' => max(0, (int) $data['oggfID']),
            'mp4fID' => max(0, (int) $data['mp4fID']),
            'posterfID' => max(0, (int) $data['posterfID']),
            'videoSize' => max(0, (int) $data['videoSize']),
        ];
        $args['width'] = $args['videoSize'] === 0 || $args['videoSize'] == 1 ? 0 : (int) $data['width'];

        parent::save($args);
    }

    public function view()
    {
        $mp4File = $this->getMp4FileObject();
        $webmFile = $this->getWebmFileObject();
        $posterFile = $this->getPosterFileObject();
        $oggFile = $this->getOggFileObject();

        $this->set('posterURL', $posterFile === null ? '' : $posterFile->getURL());
        $this->set('mp4URL', $mp4File === null ? '' : $mp4File->getURL());
        $this->set('webmURL', $webmFile === null ? '' : $webmFile->getURL());
        $this->set('oggURL', $oggFile === null ? '' : $oggFile->getURL());
    }
}
