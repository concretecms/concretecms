<?php

namespace Concrete\Core\Backup\ContentImporter\ValueInspector\InspectionRoutine;

use Concrete\Core\Backup\ContentImporter\ValueInspector\Item\PictureItem;

class PictureRoutine extends AbstractRegularExpressionRoutine
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Backup\ContentImporter\ValueInspector\InspectionRoutine\RoutineInterface::getHandle()
     */
    public function getHandle()
    {
        return 'picture';
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Backup\ContentImporter\ValueInspector\InspectionRoutine\AbstractRegularExpressionRoutine::getRegularExpression()
     */
    public function getRegularExpression()
    {
        // anything but the end of the element, where a quoted attribute may hold a ">"
        $untilTheEnd = '(?:[^>"\']|"[^"]*"|\'[^\']*\')*';

        return implode('', [
            '~',
            // group 1: the whole element, since its other attributes are kept
            '(',
            '<concrete-picture',
            // the element name ends here
            '(?=\s)',
            $untilTheEnd,
            // the attribute naming the file
            '\s(?:file)\s*=\s*',
            '(?<quote>["\'])',
            '(?:(?!\k<quote>).)+',
            '\k<quote>',
            $untilTheEnd,
            '>',
            ')',
            '~is',
        ]);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Backup\ContentImporter\ValueInspector\InspectionRoutine\AbstractRegularExpressionRoutine::getItem()
     */
    public function getItem($identifier)
    {
        $filename = '';
        $prefix = null;
        // the element without its name and its final ">" (or "/>")
        $attributes = preg_replace(['~^<concrete-picture~i', '~/?>$~'], '', $identifier);
        // the attribute naming the file is taken out: the other ones are kept as they are
        $attributes = preg_replace_callback(
            '~\s+file\s*=\s*(?<quote>["\'])(?<value>(?:(?!\k<quote>).)*)\k<quote>~si',
            static function (array $matches) use (&$filename, &$prefix): string {
                $filename = $matches['value'];
                if (strpos($filename, ':') !== false) {
                    [$prefix, $filename] = explode(':', $filename, 2);
                }

                return '';
            },
            $attributes
        );

        return new PictureItem($filename, $prefix, trim($attributes));
    }
}
