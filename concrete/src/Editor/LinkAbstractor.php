<?php

/**
 * Contains functions that converts full urls to and from
 * site pages, files (downloads and inline views), and snippets.
 * The purpose of this conversion is so that links to things
 * within a website are properly maintained when a page or file
 * is moved, or if an entire site is moved to a different directory
 * on the server (or to a different server).
 */
namespace Concrete\Core\Editor;

use Core;
use File;
use Page;
use Loader;
use URL;
use Sunra\PhpSimple\HtmlDomParser;
use Concrete\Core\Foundation\Object;

class LinkAbstractor extends Object
{
    /**
     * Takes a chunk of content containing full urls
     * and converts them to abstract link references.
     */
    private static $blackListImgAttributes = array('src', 'fid', 'data-verified', 'data-save-url');

    public static function translateTo($text)
    {
        // images inline
        $imgmatch = URL::to('/download_file', 'view_inline');
        $imgmatch = str_replace('/', '\/', $imgmatch);
        $imgmatch = str_replace('-', '\-', $imgmatch);
        $imgmatch = '/' . $imgmatch . '\/([0-9]+)/i';

        $dom = new HtmlDomParser();
        $r = $dom->str_get_html($text, true, true, DEFAULT_TARGET_CHARSET, false);
        if ($r) {
            foreach ($r->find('img') as $img) {
                $attrString = "";
                foreach ($img->attr as $key => $val) {
                    if (!in_array($key, self::$blackListImgAttributes)) {
                        $attrString .= "$key=\"$val\" ";
                    }
                }

                if (preg_match($imgmatch, $img->src, $matches)) {
                    $img->outertext = '<concrete-picture fID="' . $matches[1] . '" ' . $attrString . '/>';
                }
            }

            $text = (string) $r->restore_noise($r);
        }

        $appUrl = Core::getApplicationURL();
        if (!empty($appUrl)) {
            $url1 = str_replace('/', '\/', $appUrl . '/' . DISPATCHER_FILENAME);
            $url2 = str_replace('/', '\/', $appUrl);
            $url4 = URL::to('/download_file', 'view');
            $url4 = str_replace('/', '\/', $url4);
            $url4 = str_replace('-', '\-', $url4);
            $text = preg_replace(
                array(
                    '/' . $url1 . '\?cID=([0-9]+)/i',
                    '/' . $url4 . '\/([0-9]+)/i',
                    '/' . $url2 . '/i',
                ),
                array(
                    '{CCM:CID_\\1}',
                    '{CCM:FID_DL_\\1}',
                    '{CCM:BASE_URL}',
                ),
                $text
            );
        }

        return $text;
    }

    /**
     * Takes a chunk of content containing abstracted link references,
     * and expands them to full urls for displaying on the site front-end.
     */
    public static function translateFrom($text)
    {
        $text = preg_replace(
            array(
                '/{CCM:BASE_URL}/i',
            ),
            array(
                \Core::getApplicationURL(),
            ),
            $text
        );

        // now we add in support for the links
        $text = preg_replace_callback(
            '/{CCM:CID_([0-9]+)}/i',
            function ($matches) {
                $cID = $matches[1];
                if ($cID > 0) {
                    $c = Page::getByID($cID, 'ACTIVE');

                    return Loader::helper("navigation")->getLinkToCollection($c);
                }
            },
            $text
        );

        // now we add in support for the files that we view inline
        $dom = new HtmlDomParser();
        $r = $dom->str_get_html($text, true, true, DEFAULT_TARGET_CHARSET, false);
        if (is_object($r)) {
            foreach ($r->find('concrete-picture') as $picture) {
                $fID = $picture->fid;
                $fo = \File::getByID($fID);
                if (is_object($fo)) {
                    $style = (string) $picture->style;
                    // move width px to width attribute and height px to height attribute
                    $widthPattern = "/(?:^width|[^-]width):\\s([0-9]+)px;?/i";
                    if (preg_match($widthPattern, $style, $matches)) {
                        $style = preg_replace($widthPattern, '', $style);
                        $picture->width = $matches[1];
                    }
                    $heightPattern = "/(?:^height|[^-]height):\\s([0-9]+)px;?/i";
                    if (preg_match($heightPattern, $style, $matches)) {
                        $style = preg_replace($heightPattern, '', $style);
                        $picture->height = $matches[1];
                    }
                    if ($style === '') {
                        unset($picture->style);
                    } else {
                        $picture->style = $style;
                    }
                    $image = new \Concrete\Core\Html\Image($fo);
                    $tag = $image->getTag();

                    foreach ($picture->attr as $attr => $val) {
                        if (!in_array($attr, self::$blackListImgAttributes)) {
                            //Apply attributes to child img, if using picture tag.
                            if ($tag instanceof \Concrete\Core\Html\Object\Picture) {
                                foreach ($tag->getChildren() as $child) {
                                    if ($child instanceof \HtmlObject\Image) {
                                        $child->$attr($val);
                                    }
                                }
                            } elseif (is_callable(array($tag, $attr))) {
                                $tag->$attr($val);
                            } else {
                                $tag->setAttribute($attr, $val);
                            }
                        }
                    }

                    if (!in_array('alt', array_keys($picture->attr))) {
                        if ($tag instanceof \Concrete\Core\Html\Object\Picture) {
                            foreach ($tag->getChildren() as $child) {
                                if ($child instanceof \HtmlObject\Image) {
                                    $child->alt('');
                                }
                            }
                        } else {
                            $tag->alt('');
                        }
                    }

                    $picture->outertext = (string)$tag;
                }
            }

            $text = (string) $r->restore_noise($r);
        }

        // now we add in support for the links
        $text = preg_replace_callback(
            '/{CCM:FID_([0-9]+)}/i',
            function ($matches) {
                $fID = $matches[1];
                if ($fID > 0) {
                    $f = File::getByID($fID);
                    if (is_object($f)) {
                        return $f->getURL();
                    }
                }
            },
            $text
        );

        // now files we download
        $text = preg_replace_callback(
            '/{CCM:FID_DL_([0-9]+)}/i',
            function ($matches) {
                $fID = $matches[1];
                if ($fID > 0) {
                    $c = Page::getCurrentPage();
                    if (is_object($c)) {
                        return URL::to('/download_file', 'view', $fID, $c->getCollectionID());
                    } else {
                        return URL::to('/download_file', 'view', $fID);
                    }
                }
            },
            $text
        );

        // snippets
        $snippets = Snippet::getActiveList();
        foreach ($snippets as $sn) {
            $text = $sn->findAndReplace($text);
        }

        return $text;
    }

    /**
     * Takes a chunk of content containing abstracted link references,
     * and expands them to urls suitable for the rich text editor.
     */
    public static function translateFromEditMode($text)
    {
        $text = preg_replace(
            array(
                '/{CCM:BASE_URL}/i',
            ),
            array(
                \Core::getApplicationURL(),
            ),
            $text
        );

        //page links...
        $text = preg_replace(
            '/{CCM:CID_([0-9]+)}/i',
            \Core::getApplicationURL() . '/' . DISPATCHER_FILENAME . '?cID=\\1',
            $text
        );

        //images...
        $dom = new HtmlDomParser();
        $r = $dom->str_get_html($text, true, true, DEFAULT_TARGET_CHARSET, false);
        if (is_object($r)) {
            foreach ($r->find('concrete-picture') as $picture) {
                $fID = $picture->fid;

                $attrString = "";
                foreach ($picture->attr as $attr => $val) {
                    if (!in_array($attr, self::$blackListImgAttributes)) {
                        $attrString .= "$attr=\"$val\" ";
                    }
                }

                $picture->outertext = '<img src="' . URL::to(
                        '/download_file',
                        'view_inline',
                        $fID
                    ) . '" ' . $attrString . '/>';
            }

            $text = (string) $r->restore_noise($r);
        }

        // now we add in support for the links
        $text = preg_replace_callback(
            '/{CCM:FID_([0-9]+)}/i',
            function ($matches) {
                $fID = $matches[1];
                if ($fID > 0) {
                    return URL::to('/download_file', 'view_inline', $fID);
                }
            },
            $text
        );

        //file downloads...
        $text = preg_replace_callback(
            '/{CCM:FID_DL_([0-9]+)}/i',
            function ($matches) {
                $fID = $matches[1];
                if ($fID > 0) {
                    return URL::to('/download_file', 'view', $fID);
                }
            },
            $text
        );

        return $text;
    }

    /**
     * For the content block's getImportData() function.
     */
    public static function import($text)
    {
        $inspector = \Core::make('import/value_inspector');
        $result = $inspector->inspect((string) $text);

        return $result->getReplacedContent();
    }

    /**
     * For the content block's export() function.
     */
    public static function export($text)
    {
        $text = preg_replace_callback(
            '/{CCM:CID_([0-9]+)}/i',
            array('\Concrete\Core\Backup\ContentExporter', 'replacePageWithPlaceHolderInMatch'),
            $text
        );

        $text = preg_replace_callback(
            '/{CCM:FID_DL_([0-9]+)}/i',
            array('\Concrete\Core\Backup\ContentExporter', 'replaceFileWithPlaceHolderInMatch'),
            $text
        );

        $dom = new HtmlDomParser();
        $r = $dom->str_get_html($text, true, true, DEFAULT_TARGET_CHARSET, false);
        if (is_object($r)) {
            foreach ($r->find('concrete-picture') as $picture) {
                $fID = $picture->fid;
                $f = \File::getByID($fID);
                if (is_object($f)) {
                    $picture->fid = false;
                    $picture->file = $f->getFilename();
                }
            }
            $text = (string) $r->restore_noise($r);
        }

        return $text;
    }
}
