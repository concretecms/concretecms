<?php
namespace Concrete\Block\GoogleMap;

use Page;
use Concrete\Core\Block\BlockController;
use Config;
use Core;

class Controller extends BlockController
{
    protected $btTable = 'btGoogleMap';
    protected $btInterfaceWidth = 400;
    protected $btInterfaceHeight = 460;
    protected $btCacheBlockOutput = true;
    protected $btCacheBlockOutputOnPost = true;

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
        $error = Core::make('helper/validation/error');

        if (!trim($args['apiKey'])) {
            $error->add(t('Please enter a valid API key.'));
        }

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

    public function registerViewAssets($outputContent = '')
    {
        $this->requireAsset('javascript', 'jquery');

        $c = Page::getCurrentPage();
        if (!$c->isEditMode()) {
            $this->addFooterItem(
                '<script defer src="https://maps.googleapis.com/maps/api/js?key='
                . Config::get('app.api_keys.google.maps')
                .'"></script>'
            );
        }
    }

    public function view()
    {
        $this->set('unique_identifier', Core::make('helper/validation/identifier')->getString(18));
    }

    public function save($data)
    {
        Config::save('app.api_keys.google.maps', trim($data['apiKey']));

        $data += array(
           'title' => '',
           'location' => '',
           'zoom' => -1,
           'latitude' => 0,
           'longitude' => 0,
           'width' => null,
           'height' => null,
           'scrollwheel' => 0,
        );

        $args['title'] = trim($data['title']);
        $args['location'] = trim($data['location']);
        $args['zoom'] = (intval($data['zoom']) >= 0 && intval($data['zoom']) <= 21) ? intval($data['zoom']) : 14;
        $args['latitude'] = is_numeric($data['latitude']) ? $data['latitude'] : 0;
        $args['longitude'] = is_numeric($data['longitude']) ? $data['longitude'] : 0;
        $args['width'] = $data['width'];
        $args['height'] = $data['height'];
        $args['scrollwheel'] = $data['scrollwheel'] ? 1 : 0;

        parent::save($args);
    }
}
