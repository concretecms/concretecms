<?php
namespace Concrete\Block\Image;
use Loader;
use \File;
use Page;
use \Concrete\Core\Block\BlockController;
class Controller extends BlockController {

    protected $btInterfaceWidth = 400;
    protected $btInterfaceHeight = 550;
    protected $btTable = 'btContentImage';
    protected $btCacheBlockRecord = true;
    protected $btCacheBlockOutput = true;
    protected $btCacheBlockOutputOnPost = true;
    protected $btCacheBlockOutputForRegisteredUsers = true;
    protected $btWrapperClass = 'ccm-ui';
    protected $btExportFileColumns = array('fID','fOnstateID');
    protected $btExportPageColumns = array('internalLinkCID');
    protected $btFeatures = array(
        'image'
    );

    /**
     * Used for localization. If we want to localize the name/description we have to include this
     */
    public function getBlockTypeDescription() {
        return t("Adds images and onstates from the library to pages.");
    }

    public function getBlockTypeName() {
        return t("Image");
    }

    public function registerViewAssets() {
        // Ensure we have JQuery if we have an onState image
        if(is_object($this->getFileOnstateObject())) {
            $this->requireAsset('javascript', 'jquery');
        }
    }

    public function view() {
        $f = File::getByID($this->fID);
        if (!is_object($f) || !$f->getFileID()) {
            return false;
        }

        // onState image available
        $foS = $this->getFileOnstateObject();
        if (is_object($foS)) {
            $imgPath = array();
            $imgPath['hover'] = File::getRelativePathFromID($this->fOnstateID);
            $imgPath['default'] = File::getRelativePathFromID($this->fID);
            $this->set('imgPath', $imgPath);
            $this->set('foS', $foS);
        }

        $this->set('f', $f);
        $this->set('altText',$this->getAltText());
        $this->set('title',$this->getTitle());
        $this->set('linkURL',$this->getLinkURL());
    }

    public function getJavaScriptStrings() {
        return array(
            'image-required' => t('You must select an image.')
        );
    }

    public function isComposerControlDraftValueEmpty() {
        $f = $this->getFileObject();
        if (is_object($f) && !$f->isError()) {
            return false;
        }
        return true;
    }

    public function getImageFeatureDetailFileObject() {
        // i don't know why this->fID isn't sticky in some cases, leading us to query
        // every damn time
        $db = Loader::db();
        $fID = $db->GetOne('select fID from btContentImage where bID = ?', array($this->bID));
        if ($fID) {
            $f = File::getByID($fID);
            if (is_object($f) && !$f->isError()) {
                return $f;
            }
        }
    }

    function getFileID() {return $this->fID;}
    function getFileOnstateID() {return $this->fOnstateID;}
    public function getFileOnstateObject() {
        if($this->fOnstateID) {
            return File::getByID($this->fOnstateID);
        }
    }
    public function getFileObject() {
        return File::getByID($this->fID);
    }
    function getAltText() {return $this->altText;}
    function getTitle() {return $this->title;}
    function getExternalLink() {return $this->externalLink;}
    function getInternalLinkCID() {return $this->internalLinkCID;}
    function getLinkURL() {
        if (!empty($this->externalLink)) {
            $sec = \Core::make('helper/security');
            return $sec->sanitizeURL($this->externalLink);
        } else if (!empty($this->internalLinkCID)) {
            $linkToC = Page::getByID($this->internalLinkCID);
            return (empty($linkToC) || $linkToC->error) ? '' : Loader::helper('navigation')->getLinkToCollection($linkToC);
        } else {
            return '';
        }
    }

    public function validate_composer() {
        $f = $this->getFileObject();
        $e = Loader::helper('validation/error');
        if (!is_object($f) || $f->isError() || !$f->getFileID()) {
            $e->add(t('You must specify a valid image file.'));
        }
        return $e;
    }

    public function save($args) {
        $args['fID'] = ($args['fID'] != '') ? $args['fID'] : 0;
        $args['fOnstateID'] = ($args['fOnstateID'] != '') ? $args['fOnstateID'] : 0;
        $args['maxWidth'] = (intval($args['maxWidth']) > 0) ? intval($args['maxWidth']) : 0;
        $args['maxHeight'] = (intval($args['maxHeight']) > 0) ? intval($args['maxHeight']) : 0;
        if (!$args['constrainImage']) {
            $args['maxWidth'] = 0;
            $args['maxHeight'] = 0;
        }
        switch (intval($args['linkType'])) {
            case 1:
                $args['externalLink'] = '';
                break;
            case 2:
                $args['internalLinkCID'] = 0;
                break;
            default:
                $args['externalLink'] = '';
                $args['internalLinkCID'] = 0;
                break;
        }
        unset($args['linkType']); //this doesn't get saved to the database (it's only for UI usage)
        parent::save($args);
    }

}
