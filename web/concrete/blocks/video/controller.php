<?php

namespace Concrete\Block\Video;

use File;
use Concrete\Core\Block\BlockController;

class Controller extends BlockController
{
    protected $btInterfaceWidth = 320;
    protected $btInterfaceHeight = 270;
    protected $btTable = 'btVideo';
    protected $btCacheBlockRecord = true;
    protected $btCacheBlockOutput = true;
    protected $btCacheBlockOutputOnPost = true;
    protected $btCacheBlockOutputForRegisteredUsers = false;
    protected $btExportFileColumns = array('fID');
    protected $btWrapperClass = 'ccm-ui';

    public $width = '';
    public $height = '';
    public $fID = 0;

    /** 
     * Used for localization. If we want to localize the name/description we have to include this.
     */
    public function getBlockTypeDescription()
    {
        return t("Embeds uploaded video into a web page. Supports WebM, Ogg, and Quicktime/MPEG4 formats.");
    }

    public function getBlockTypeName()
    {
        return t("Video Player");
    }

    public function getMp4FileID()
    {
        return $this->mp4fID;
    }
    public function getWebmFileID()
    {
        return $this->webmfID;
    }
    public function getOggFileID()
    {
        return $this->oggfID;
    }
    public function getPosterFileID()
    {
        return $this->posterfID;
    }

    public function getMp4FileObject()
    {
        return File::getByID($this->mp4fID);
    }

    public function getOggFileObject()
    {
        return File::getByID($this->oggfID);
    }

    public function getWebmFileObject()
    {
        return File::getByID($this->webmfID);
    }

    public function getPosterFileObject()
    {
        return File::getByID($this->posterfID);
    }

    public function save($data)
    {
        $args['webmfID'] = (intval($data['webmfID']) > 0 ? intval($data['webmfID']) : 0);
        $args['oggfID'] = (intval($data['oggfID']) > 0 ? intval($data['oggfID']) : 0);
        $args['mp4fID'] = (intval($data['mp4fID']) > 0 ? intval($data['mp4fID']) : 0);
        $args['posterfID'] = (intval($data['posterfID']) > 0 ? intval($data['posterfID']) : 0);
        $args['width'] = (intval($data['width']) > 0)  ? intval($data['width'])  : 425;
        $args['height'] = (intval($data['height']) > 0) ? intval($data['height']) : 334;

        parent::save($args);
    }

    public function registerViewAssets($outputContent = '')
    {
        $this->requireAsset('swfobject');
    }

    public function view()
    {
        $mp4File = $this->getMp4FileObject();
        $webmFile = $this->getWebmFileObject();
        $posterFile = $this->getPosterFileObject();
        $oggFile = $this->getOggFileObject();
        if (is_object($posterFile)) {
            $this->set('posterURL', $posterFile->getURL());
        }
        if (is_object($mp4File)) {
            $this->set('mp4URL', $mp4File->getURL());
        }
        if (is_object($webmFile)) {
            $this->set('webmURL', $webmFile->getURL());
        }
        if (is_object($oggFile)) {
            $this->set('oggURL', $oggFile->getURL());
        }
    }
}
