<?php

namespace Concrete\Core\File\Image\Thumbnail;

use Concrete\Core\Entity\File\Version as FileVersion;
use Concrete\Core\File\Image\Thumbnail\Type\Version as ThumbnailTypeVersion;
use HtmlObject\Element;

class ThumbnailPlaceholderService
{
    /**
     * @param int $width
     * @param int $height
     * @param string $placeholderBackgroundColor
     * @return Element
     */
    public function getThumbnailImage(
        int $width,
        int $height,
        string $placeholderBackgroundColor = "#4c4f56"
    )
    {
        return new Element(
            "svg",
            new Element(
                "rect",
                "",
                [
                    "width" => (string)$width,
                    "height" => (string)$height
                ]
            ),
            [
                "xmlns" => "http://www.w3.org/2000/svg",
                "role" => "img",
                "fill" => $placeholderBackgroundColor,
                "width" => "100%",
                "viewBox" => sprintf("0 0 %s %s",
                    (string)$width,
                    (string)$height
                ),
                "style" => sprintf(
                    "max-width: %s;",
                    (string)$width
                ),
                "class" => "placeholder"
            ]
        );
    }

    /**
     * Generates a SVG placeholder graphic with wrapped in an div container that contains all required attributes to
     * swap out this placeholder image by mercure service.
     *
     * @param FileVersion $fileVersion
     * @param ThumbnailTypeVersion $thumbnailType
     * @param array $attributes
     * @param string $placeholderBackgroundColor
     * @return string
     */
    public function getThumbnailPlaceholder(
        FileVersion $fileVersion,
        ThumbnailTypeVersion $thumbnailType,
        array $attributes = [],
        string $placeholderBackgroundColor = "#4c4f56"
    )
    {
        $defaults = [
            "class" => "ccm-image-wrapper placeholder-glow",
            "data-thumbnail-type-handle" => $thumbnailType->getHandle(),
            "data-file-id" => (string)$fileVersion->getFileID(),
            "data-file-version-id" => (string)$fileVersion->getFileVersionID()
        ];

        if (isset($attributes['class'])) {
            $defaults['class'] .= " " . $attributes['class'];
        }

        foreach ($defaults as $key => $val) {
            if (isset($attributes[$key])) {
                unset($attributes[$key]);
            }
        }

        $attributes = array_merge($attributes, $defaults);

        if(!empty($thumbnailType->getHeight())){
            $thumbnailHeight = $thumbnailType->getHeight();
        } else {
            $thumbnailHeight = $thumbnailType->getWidth();
        }

        return (string)new Element(
            "div",
            [
                $this->getThumbnailImage(
                    $thumbnailType->getWidth(),
                    $thumbnailHeight,
                    $placeholderBackgroundColor
                ),
                new Element(
                    "div",
                    "",
                    [
                        "class" => "ccm-image-html",
                        "style" => "display: none;"
                    ]
                )
            ],
            $attributes
        );
    }
}