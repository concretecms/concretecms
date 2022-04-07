<?php

namespace Concrete\Block\GoogleMap;

use Concrete\Core\Block\BlockController;
use Concrete\Core\Feature\Features;
use Concrete\Core\Feature\UsesFeatureInterface;
use Concrete\Core\Page\Page;
use Config;

class Controller extends BlockController implements UsesFeatureInterface
{
    protected $btTable = 'btGoogleMap';

    protected $btInterfaceWidth = 525;

    protected $btInterfaceHeight = 550;

    protected $btCacheBlockOutput = true;

    protected $btCacheBlockOutputOnPost = true;

    public function getBlockTypeDescription()
    {
        return t('Enter an address and a Google Map of that location will be placed in your page.');
    }

    public function getBlockTypeName()
    {
        return t('Google Map');
    }

    public function getRequiredFeatures(): array
    {
        return [
            Features::MAPS,
        ];
    }

    public function add()
    {
        $this->set('title', '');
        $this->set('location', '');
        $this->set('latitude', '');
        $this->set('longitude', '');
        $this->set('zoom', 14);
        $this->set('width', '100%');
        $this->set('height', '400px');
        $this->set('scrollwheel', 1);
        $this->set('titleFormat', 'h3');
    }

    public function validate($args)
    {
        $error = $this->app->make('helper/validation/error');

        if (!trim($args['apiKey'])) {
            $error->add(t('Please enter a valid API key.'));
        }

        if (empty($args['location']) || $args['latitude'] === '' || $args['longitude'] === '') {
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
                '<script defer src="https://maps.googleapis.com/maps/api/js?callback=concreteGoogleMapInit&key='
                . Config::get('app.api_keys.google.maps')
                . '"></script>'
            );
        }
    }

    public function save($data)
    {
        $data += [
            'title' => '',
            'location' => '',
            'zoom' => -1,
            'latitude' => 0,
            'longitude' => 0,
            'width' => null,
            'height' => null,
            'scrollwheel' => 0,
            'apiKey' => '',
            'titleFormat' => 'h3',
        ];

        Config::save('app.api_keys.google.maps', trim($data['apiKey']));

        $args['title'] = trim($data['title']);
        $args['location'] = trim($data['location']);
        $args['zoom'] = ((int) ($data['zoom']) >= 0 && (int) ($data['zoom']) <= 21) ? (int) ($data['zoom']) : 14;
        $args['latitude'] = !empty($data['latitude']) ? $data['latitude'] : 0;
        $args['longitude'] = !empty($data['longitude']) ? $data['longitude'] : 0;
        $args['width'] = $data['width'];
        $args['height'] = $data['height'];
        $args['scrollwheel'] = $data['scrollwheel'] ? 1 : 0;
        $args['titleFormat'] = $data['titleFormat'];

        parent::save($args);
    }
}
