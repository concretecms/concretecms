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

use Concrete\Core\Backup\ContentExporter;
use Concrete\Core\Entity\File\File;
use Concrete\Core\Foundation\ConcreteObject;
use Concrete\Core\Html\Image;
use Concrete\Core\Html\Object\Picture;
use Concrete\Core\Page\Page;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Sunra\PhpSimple\HtmlDomParser;

class LinkAbstractor extends ConcreteObject
{
    /**
     * Takes a chunk of content containing full urls
     * and converts them to abstract link references.
     */
    private static $blackListImgAttributes = ['src', 'fid', 'data-verified', 'data-save-url'];

    /**
     * @param $text
     *
     * @return string
     */
    public static function translateTo($text)
    {
        $app = Application::getFacadeApplication();
        $resolver = $app->make(ResolverManagerInterface::class);

        // images inline
        $imgmatch = $resolver->resolve(['/download_file', 'view_inline']);
        $imgmatch = str_replace(['/', '-'], ['\/', '\-'], $imgmatch);
        $imgmatch = '/' . $imgmatch . '\/([a-f0-9-]{36}|[0-9]+)/i';

        $dom = new HtmlDomParser();
        $r = $dom->str_get_html($text, true, true, DEFAULT_TARGET_CHARSET, false);
        if ($r) {
            foreach ($r->find('img') as $img) {
                $attrString = '';
                foreach ($img->attr as $key => $val) {
                    if (!in_array($key, self::$blackListImgAttributes)) {
                        $attrString .= "{$key}=\"{$val}\" ";
                    }
                }

                if (preg_match($imgmatch, $img->src, $matches)) {
                    $img->outertext = '<concrete-picture fID="' . $matches[1] . '" ' . $attrString . '/>';
                }
            }

            $text = (string) $r->restore_noise($r);
        }

        $appUrl = Application::getApplicationURL();
        if (!empty($appUrl)) {
            $url1 = str_replace('/', '\/', $appUrl . '/' . DISPATCHER_FILENAME);
            $url2 = str_replace('/', '\/', $appUrl);
            $url4 = $resolver->resolve(['/download_file', 'view']);
            $url4 = str_replace(['/', '-'], ['\/', '\-'], $url4);
            $text = preg_replace(
                [
                    '/' . $url1 . '\?cID=([0-9]+)/i',
                    '/' . $url4 . '\/([a-f0-9-]{36}|[0-9]+)/i',
                    '/' . $url2 . '/i',
                ],
                [
                    '{CCM:CID_\\1}',
                    '{CCM:FID_DL_\\1}',
                    '{CCM:BASE_URL}',
                ],
                $text
            );
        }

        return (string) $text;
    }

    /**
     * Takes a chunk of content containing abstracted link references,
     * and expands them to full urls for displaying on the site front-end.
     *
     * @param mixed $text
     *
     * @return string
     */
    public static function translateFrom($text)
    {
        $app = Application::getFacadeApplication();
        $entityManager = $app->make(EntityManagerInterface::class);
        $resolver = $app->make(ResolverManagerInterface::class);

        $text = preg_replace(
            [
                '/{CCM:BASE_URL}/i',
            ],
            [
                Application::getApplicationURL(),
            ],
            $text
        );

        // now we add in support for the links
        $text = static::replacePlaceholder(
            $text,
            '{CCM:CID_([0-9]+)}',
            function ($cID) use ($resolver) {
                if ($cID > 0) {
                    $c = Page::getByID($cID, 'ACTIVE');
                    if ($c->isActive()) {
                        return $resolver->resolve([$c]);
                    }
                }

                return '';
            }
        );

        // now we add in support for the files that we view inline
        $dom = new HtmlDomParser();
        $r = $dom->str_get_html($text, true, true, DEFAULT_TARGET_CHARSET, false);
        if (is_object($r)) {
            foreach ($r->find('concrete-picture') as $picture) {
                $fID = $picture->fid;
                if (uuid_is_valid($fID)) {
                    $fo = \Concrete\Core\File\File::getByUUID($fID);
                } else {
                    $fo = \Concrete\Core\File\File::getByID($fID);
                }
                if ($fo !== null) {
                    $style = (string) $picture->style;
                    // move width px to width attribute and height px to height attribute
                    $widthPattern = '/(?:^width|[^-]width):\\s([0-9]+)px;?/i';
                    if (preg_match($widthPattern, $style, $matches)) {
                        $style = preg_replace($widthPattern, '', $style);
                        $picture->width = $matches[1];
                    }
                    $heightPattern = '/(?:^height|[^-]height):\\s([0-9]+)px;?/i';
                    if (preg_match($heightPattern, $style, $matches)) {
                        $style = preg_replace($heightPattern, '', $style);
                        $picture->height = $matches[1];
                    }
                    if ($style === '') {
                        unset($picture->style);
                    } else {
                        $picture->style = $style;
                    }
                    $image = new Image($fo);
                    $tag = $image->getTag();

                    foreach ($picture->attr as $attr => $val) {
                        $attr = (string) $attr;
                        if (!in_array($attr, self::$blackListImgAttributes)) {
                            //Apply attributes to child img, if using picture tag.
                            if ($tag instanceof Picture) {
                                foreach ($tag->getChildren() as $child) {
                                    if ($child instanceof \HtmlObject\Image) {
                                        $child->{$attr}($val);
                                    }
                                }
                            } elseif (is_callable([$tag, $attr])) {
                                $tag->{$attr}($val);
                            } else {
                                $tag->setAttribute($attr, $val);
                            }
                        }
                    }

                    if (!in_array('alt', array_keys($picture->attr))) {
                        if ($tag instanceof Picture) {
                            foreach ($tag->getChildren() as $child) {
                                if ($child instanceof \HtmlObject\Image) {
                                    $child->alt('');
                                }
                            }
                        } else {
                            $tag->alt('');
                        }
                    }

                    $picture->outertext = (string) $tag;
                }
            }

            $text = (string) $r->restore_noise($r);
        }

        // now we add in support for the links
        $text = static::replacePlaceholder(
            $text,
            '{CCM:FID_([a-f0-9-]{36}|[0-9]+)}',
            function ($fID) {
                if ($fID) {
                    if (uuid_is_valid($fID)) {
                        $f = \Concrete\Core\File\File::getByUUID($fID);
                    } else {
                        $f = \Concrete\Core\File\File::getByID($fID);
                    }
                    if ($f !== null) {
                        return $f->getURL();
                    }
                }

                return '';
            }
        );

        // now files we download
        $currentPage = null;
        $text = static::replacePlaceholder(
            $text,
            '{CCM:FID_DL_([a-f0-9-]{36}|[0-9]+)}',
            function ($fID) use ($resolver, &$currentPage) {
                if ($fID) {
                    $args = ['/download_file', 'view', $fID];
                    if ($currentPage === null) {
                        $currentPage = Page::getCurrentPage();
                        if (!$currentPage || $currentPage->isError()) {
                            $currentPage = false;
                        }
                    }
                    if ($currentPage !== false) {
                        $args[] = $currentPage->getCollectionID();
                    }
                    return $resolver->resolve($args);
                }

                return '';
            }
        );

        // snippets
        if (strrpos($text, 'data-scs') !== false) {
            $snippets = Snippet::getActiveList();
            foreach ($snippets as $sn) {
                $text = $sn->findAndReplace($text);
            }
        }

        return $text;
    }

    /**
     * Takes a chunk of content containing abstracted link references,
     * and expands them to urls suitable for the rich text editor.
     *
     * @param mixed $text
     *
     * @return string
     */
    public static function translateFromEditMode($text)
    {
        $app = Application::getFacadeApplication();
        $resolver = $app->make(ResolverManagerInterface::class);
        $appUrl = Application::getApplicationURL();

        $text = preg_replace(
            [
                '/{CCM:BASE_URL}/i',
            ],
            [
                $appUrl,
            ],
            $text
        );

        //page links...
        $text = preg_replace(
            '/{CCM:CID_([0-9]+)}/i',
            $appUrl . '/' . DISPATCHER_FILENAME . '?cID=\\1',
            $text
        );

        //images...
        $dom = new HtmlDomParser();
        $r = $dom->str_get_html($text, true, true, DEFAULT_TARGET_CHARSET, false);
        if (is_object($r)) {
            foreach ($r->find('concrete-picture') as $picture) {
                $fID = $picture->fid;

                $attrString = '';
                foreach ($picture->attr as $attr => $val) {
                    if (!in_array($attr, self::$blackListImgAttributes)) {
                        $attrString .= "{$attr}=\"{$val}\" ";
                    }
                }

                if (uuid_is_valid($fID)) {
                    $file = \Concrete\Core\File\File::getByUUID($fID);
                } else {
                    $file = \Concrete\Core\File\File::getByID($fID);
                }

                if ($file instanceof File && $file->hasFileUUID()) {
                    $picture->outertext = '<img src="' . $resolver->resolve([
                            '/download_file',
                            'view_inline',
                            $file->getFileUUID(),
                        ]) . '" ' . $attrString . '/>';
                } else {
                    $picture->outertext = '<img src="' . $resolver->resolve([
                            '/download_file',
                            'view_inline',
                            $fID,
                        ]) . '" ' . $attrString . '/>';
                }

            }

            $text = (string) $r->restore_noise($r);
        }

        // now we add in support for the links
        $text = static::replacePlaceholder(
            $text,
            '{CCM:FID_([a-f0-9-]{36}|[0-9]+)}',
            function ($fID) use ($resolver) {
                if ($fID) {
                    if (uuid_is_valid($fID)) {
                        $file = \Concrete\Core\File\File::getByUUID($fID);
                    } else {
                        $file = \Concrete\Core\File\File::getByID($fID);
                    }
                    if ($file instanceof File && $file->hasFileUUID()) {
                        return $resolver->resolve(['/download_file', 'view_inline', $file->getFileUUID()]);
                    } else {
                        return $resolver->resolve(['/download_file', 'view_inline', $fID]);
                    }
                }

                return '';
            }
        );

        //file downloads...
        $text = static::replacePlaceholder(
            $text,
            '{CCM:FID_DL_([a-f0-9-]{36}|[0-9]+)}',
            function ($fID) use ($resolver) {
                if ($fID) {
                    return $resolver->resolve(['/download_file', 'view', $fID]);
                }

                return '';
            }
        );

        return $text;
    }

    /**
     * For the content block's getImportData() function.
     *
     * @param string $text
     *
     * @return string
     */
    public static function import($text)
    {
        $app = Application::getFacadeApplication();
        $inspector = $app->make('import/value_inspector');
        $result = $inspector->inspect((string) $text);

        return $result->getReplacedContent();
    }

    /**
     * For the content block's export() function.
     *
     * @param mixed $text
     *
     * @return string
     */
    public static function export($text)
    {
        $app = Application::getFacadeApplication();
        $entityManager = $app->make(EntityManagerInterface::class);

        $text = static::replacePlaceholder(
            $text,
            '{CCM:CID_([0-9]+)}',
            function ($cID) {
                return ContentExporter::replacePageWithPlaceHolder($cID);
            }
        );

        $text = static::replacePlaceholder(
            $text,
            '{CCM:FID_DL_([a-f0-9-]{36}|[0-9]+)}',
            function ($fID) {
                return ContentExporter::replaceFileWithPlaceHolder($fID);
            }
        );

        $dom = new HtmlDomParser();
        $r = $dom->str_get_html($text, true, true, DEFAULT_TARGET_CHARSET, false);
        if (is_object($r)) {
            foreach ($r->find('concrete-picture') as $picture) {
                $fID = $picture->fid;
                $f = $entityManager->find(File::class, $fID);
                if (is_object($f)) {
                    $picture->fid = false;
                    $picture->file = $f->getPrefix() . ':' . $f->getFilename();
                }
            }
            $text = (string) $r->restore_noise($r);
        }

        return $text;
    }

    /**
     * Replace a placeholder.
     *
     * @param string $text the text that may contain placeholders to be replaced
     * @param string $pattern the regular expression (without enclosing '/') that captures the placeholder
     * @param callable $resolver a callback that replaces the captured placeholder value
     * @param bool $caseSensitive is $pattern case sensitive?
     *
     * @return string
     *
     * @since concrete5 8.5.0a3
     */
    protected static function replacePlaceholder($text, $pattern, callable $resolver, $caseSensitive = false)
    {
        $regex = "/{$pattern}/";
        if (!$caseSensitive) {
            $regex .= 'i';
        }
        if (!preg_match_all($regex, $text, $matches)) {
            return $text;
        }
        $replaces = array_combine($matches[0], $matches[1]);
        if (!$caseSensitive) {
            $replaces = array_change_key_case($replaces, CASE_UPPER);
        }
        foreach (array_keys($replaces) as $key) {
            $replaces[$key] = (string) $resolver($replaces[$key]);
        }

        return $caseSensitive ? strtr($text, $replaces) : str_ireplace(array_keys($replaces), array_values($replaces), $text);
    }
}
