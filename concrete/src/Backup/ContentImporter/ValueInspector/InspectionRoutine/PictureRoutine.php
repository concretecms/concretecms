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
        // anything except ">", but quoted attribute values may contain ">"
        $upToClosing = '(?:[^>"\']|"[^"]*"|\'[^\']*\')*';

        return implode('', [
            // delimiter
            '~',
            // group 1: the complete <concrete-picture> element
            '(',
            // element name
            '<concrete-picture',
            // followed by whitespace (but don't consume it)
            '(?=\s)',
            // followed by anything except ">"
            $upToClosing,
            // a "file" or "file-id" attribute, preceded by whitespace
            '\s(file|file-id)\s*=\s*',
            // opening quote
            '(?<quote>["\'])',
            // at least one character (but not the char used to quote)
            '((?!\k<quote>).)+',
            // closing quote
            '\k<quote>',
            // followed by anything except ">"
            $upToClosing,
            // until the closing ">"
            '>',
            // end of group 1
            ')',
            // case-insensitive, "." also matches newlines
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
        $fileID = '';
        // strip the element name and the final ">" (or "/>")
        $attributes = preg_replace(['~^<concrete-picture~i', '~/?>$~'], '', $identifier);
        // extract (and remove) the "file" and "file-id" attributes
        $attributes = preg_replace_callback(
            '~\s+(?<name>file|file-id)\s*=\s*(?<quote>["\'])(?<value>(?:(?!\k<quote>).)*)\k<quote>~si',
            static function (array $m) use (&$filename, &$prefix, &$fileID) {
                switch (strtolower($m['name'])) {
                    case 'file':
                        $filename = $m['value'];
                        if ($filename !== '') {
                            $fileID = '';
                            if (str_contains($m['value'], ':')) {
                                [$prefix, $filename] = explode(':', $m['value'], 2);
                            }
                        }
                        break;
                    case 'file-id':
                        if ($filename === '') {
                            $fileID = PictureItem::parseFileIdentifier($m['value']);
                        }
                        break;
                }

                return '';
            },
            $attributes
        );

        return new PictureItem($filename, $prefix, $fileID, trim($attributes));
    }
}
