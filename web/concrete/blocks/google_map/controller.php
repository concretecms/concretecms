<?php
namespace Concrete\Block\GoogleMap;

use Loader;
use Page;
use \Concrete\Core\Block\BlockController;

class Controller extends BlockController
{

    protected $btTable = 'btGoogleMap';
    protected $btInterfaceWidth = "400";
    protected $btInterfaceHeight = "320";
    protected $btCacheBlockRecord = true;
    protected $btCacheBlockOutput = true;
    protected $btCacheBlockOutputOnPost = true;
    protected $btCacheBlockOutputForRegisteredUsers = false;

    public $title = "";
    public $location = "";
    public $latitude = "";
    public $longitude = "";
    public $scrollwheel = true;
    public $zoom = 14;

    /**
     * Used for localization. If we want to localize the name/description we have to include this
     */
    public function getBlockTypeDescription()
    {
        return t("Enter an address and a Google Map of that location will be placed in your page.");
    }

    public function getBlockTypeName()
    {
        return t("Google Map");
    }


    public function validate($args)
    {
        $error = Loader::helper('validation/error');

        if (empty($args['location']) || $args['latitude'] === '' || $args['longtitude'] === '') {
            $error->add(t('You must select a valid location.'));
        }

        if (!is_numeric($args['zoom'])) {
            $error->add(t('Please enter a zoom number from 0 to 21.'));
        }

        if ($error->has()) {
            return $error;
        }
    }

    public function registerViewAssets()
    {
        $this->requireAsset('javascript', 'jquery');
        $this->addFooterItem(
            '<script type="text/javascript" src="//maps.google.com/maps/api/js?sensor=true"></script>'
        );
    }

    public function view()
    {
        $this->set('bID', $this->bID);
        $this->set('title', $this->title);
        $this->set('location', $this->location);
        $this->set('latitude', $this->latitude);
        $this->set('longitude', $this->longitude);
        $this->set('zoom', $this->zoom);
        $this->set('scrollwheel', $this->scrollwheel);
    }

    public function save($data)
    {
        $args['title'] = isset($data['title']) ? trim($data['title']) : '';
        $args['location'] = isset($data['location']) ? trim($data['location']) : '';
        $args['zoom'] = (intval($data['zoom']) >= 0 && intval($data['zoom']) <= 21) ? intval($data['zoom']) : 14;
        $args['latitude'] = is_numeric($data['latitude']) ? $data['latitude'] : 0;
        $args['longitude'] = is_numeric($data['longitude']) ? $data['longitude'] : 0;
        $args['width'] = $data['width'];
        $args['height'] = $data['height'];
        $args['scrollwheel'] = $data['scrollwheel'] ? 1 : 0;
        parent::save($args);
    }

}
