<?php
defined('C5_EXECUTE') or die('Access Denied.');

$json = [
    [
        "id" => 1,
        "title" => "Newspaper Man",
        "description" => 'A picture of a man holding a newspaper.',
        "extension" => 'jpg',
        "attributes" => [],
        "fileSize" => '240 kb',
        "imageUrl" => 'https://images.pexels.com/photos/4348556/pexels-photo-4348556.jpeg?auto=compress&cs=tinysrgb&dpr=2&h=750&w=1260',
        "thumbUrl" => 'https://images.pexels.com/photos/4348556/pexels-photo-4348556.jpeg?auto=compress&cs=tinysrgb&dpr=2&h=750&w=1260',
        "detailUrl" => 'https://www.google.com/',
        "displayChoices" => [
            "gallery-specific-options" => [
                "value" => '',
                "title" => 'Gallery Specific Options',
                "type" => 'text'
            ],
            "size" => [
                "options" => [
                    "square" => 'Square Image',
                    "default" => 'Keep Image Aspect Ratio'
                ]
            ]
        ]
    ]
];

?>

<div id="ccm-gallery-<?= $bID ?>">
    <GalleryEdit :gallery="data"/>
</div>

<script>
    Concrete.Vue.activateContext('gallery', function (Vue, config) {
        new Vue({
            el: '#ccm-gallery-<?= $bID ?>',
            components: config.components,
            data: function() {
                return {
                    data: JSON.parse(<?= json_encode($json) ?>)
                }
            }
        })

    })

</script>
