<?php

use Concrete\Core\Device\DeviceInterface as DeviceType;

/*
 * A list of devices and their information
 * Make sure to make the "width" the larger number
 */

return array(

    # APPLE

    /* iPhone 6, 1334x750 @2x */
    'iphone6' => array(
        'type' => DeviceType::MOBILE,
        'name' => 'iPhone 6',
        'class' => '\Concrete\Core\Device\Apple\IPhone\IPhone6Device',
        'width' => 1334,
        'height' => 750,
        'pixel_ratio' => 2,
        'default_orientation' => 'portrait',
        'agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 8_1_2 like Mac OS X) AppleWebKit/600.1.4 (KHTML, like Gecko) Version/8.0 Mobile/12B440 Safari/600.1.4',
    ),

    /* iPhone 6 Plus, 1920x1080 @2.6x */
    'iphone6plus' => array(
        'type' => DeviceType::MOBILE,
        'name' => 'iPhone 6 Plus',
        'class' => '\Concrete\Core\Device\Apple\IPhone\IPhone6PlusDevice',
        'width' => 1920,
        'height' => 1080,
        'pixel_ratio' => 2.6,
        'default_orientation' => 'portrait',
        'agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 8_1_2 like Mac OS X) AppleWebKit/600.1.4 (KHTML, like Gecko) Version/8.0 Mobile/12B440 Safari/600.1.4',
    ),

    /* iPhone 5, 1136x640 @2x */
    'iphone5' => array(
        'type' => DeviceType::MOBILE,
        'name' => 'iPhone 5s',
        'class' => '\Concrete\Core\Device\Apple\IPhone\IPhone5Device',
        'width' => 1136,
        'height' => 640,
        'pixel_ratio' => 2,
        'default_orientation' => 'portrait',
        'agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 8_1_2 like Mac OS X) AppleWebKit/600.1.4 (KHTML, like Gecko) Version/8.0 Mobile/12B440 Safari/600.1.4',
    ),

    /* iPad, 2048x1536 @2x*/
    'ipad' => array(
        'type' => DeviceType::TABLET,
        'name' => 'iPad',
        'class' => '\Concrete\Core\Device\Apple\IPad\IPadDevice',
        'width' => 2048,
        'height' => 1536,
        'pixel_ratio' => 2,
    ),

    # SAMSUNG

    /* Galaxy S5 */
    's5' => array(
        'type' => DeviceType::MOBILE,
        'name' => 'Samsung Galaxy S5',
        'class' => '\Concrete\Core\Device\Samsung\Galaxy\S5Device',
        'width' => 1920,
        'height' => 1080,
        'pixel_ratio' => 3,
        'default_orientation' => 'portrait',
        'agent' => 'Mozilla/5.0 (Linux; Android 5.0; pl-pl; SAMSUNG SM-G900F/G900FXXU1BNL9 Build/LRX21T) AppleWebKit/537.36 (KHTML, like Gecko) Version/2.1 Chrome/34.0.1847.76 Mobile Safari/537.36',
    ),

);
