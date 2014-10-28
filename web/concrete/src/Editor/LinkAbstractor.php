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
use File;
use Page;
use Loader;
use URL;
use Sunra\PhpSimple\HtmlDomParser;
use \Concrete\Core\Foundation\Object;
class LinkAbstractor extends Object {

	/**
	 * Takes a chunk of content containing full urls
	 * and converts them to abstract link references.
	 */
	public static function translateTo($text) {
		// keep links valid
		if (!defined('BASE_URL') || BASE_URL == '') {
			return $text;
		}

		$url1 = str_replace('/', '\/', BASE_URL . DIR_REL . '/' . DISPATCHER_FILENAME);
		$url2 = str_replace('/', '\/', BASE_URL . DIR_REL);
		$url4 = URL::to('/download_file', 'view');
		$url4 = str_replace('/', '\/', $url4);
		$url4 = str_replace('-', '\-', $url4);
		$text = preg_replace(
			array(
				'/' . $url1 . '\?cID=([0-9]+)/i',
				'/' . $url4 . '\/([0-9]+)/i',
				'/' . $url2 . '/i'),
			array(
				'{CCM:CID_\\1}',
				'{CCM:FID_DL_\\1}',
				'{CCM:BASE_URL}')
			, $text);

		// images inline
		$imgmatch = URL::to('/download_file', 'view_inline');
		$imgmatch = str_replace('/', '\/', $imgmatch);
		$imgmatch = str_replace('-', '\-', $imgmatch);
		$imgmatch = '/' . $imgmatch . '\/([0-9]+)/i';

		$dom = new HtmlDomParser();
		$r = $dom->str_get_html($text);
		if ($r) {
			foreach($r->find('img') as $img) {
				$src = $img->src;
				$alt = $img->alt;
				$style = $img->style;
				if (preg_match($imgmatch, $src, $matches)) {
					$img->outertext = '<concrete-picture fID="' . $matches[1] . '" alt="' . $alt . '" style="' . $style . '" />';
				}
			}

			$text = (string) $r;
		}

		return $text;
	}

	/**
	 * Takes a chunk of content containing abstracted link references,
	 * and expands them to full urls for displaying on the site front-end.
	 */
	public static function translateFrom($text) {

		$text = preg_replace(
			array(
				'/{CCM:BASE_URL}/i'
			),
			array(
				BASE_URL . DIR_REL,
			),
			$text);

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
			$text);

		// now we add in support for the files that we view inline
		$dom = new HtmlDomParser();
		$r = $dom->str_get_html($text);
		if (is_object($r)) {
			foreach($r->find('concrete-picture') as $picture) {
				$fID = $picture->fid;
				$alt = $picture->alt;
				$style = $picture->style;
				$fo = \File::getByID($fID);
				if (is_object($fo)) {
					if ($style) {
						$image = new \Concrete\Core\Html\Image($fo, false);
						$image->getTag()->width(false)->height(false);
					} else {
						$image = new \Concrete\Core\Html\Image($fo);
					}
					$tag = $image->getTag();
					if ($alt) {
						$tag->alt($alt);
					}
					if ($style) {
						$tag->style($style);
					}
					$picture->outertext = (string) $tag;
				}
			}

			$text = (string) $r;
		}

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
			$text);

		// snippets
		$snippets = Snippet::getActiveList();
		foreach($snippets as $sn) {
			$text = $sn->findAndReplace($text);
		}
		return $text;
	}

	/**
	 * Takes a chunk of content containing abstracted link references,
	 * and expands them to urls suitable for the rich text editor.
	 */
	public static function translateFromEditMode($text) {
		//page links...
		$text = preg_replace(
			'/{CCM:CID_([0-9]+)}/i',
			BASE_URL . DIR_REL . '/' . DISPATCHER_FILENAME . '?cID=\\1',
			$text);

		//images...
		$dom = new HtmlDomParser();
		$r = $dom->str_get_html($text);
		if (is_object($r)) {
			foreach($r->find('concrete-picture') as $picture) {
				$fID = $picture->fid;
				$alt = $picture->alt;
				$style = $picture->style;
				$picture->outertext = '<img src="' . URL::to('/download_file', 'view_inline', $fID) . '" alt="' . $alt . '" style="' . $style . '" />';
			}

			$text = (string) $r;
		}

		//file downloads...
		$text = preg_replace_callback(
			'/{CCM:FID_DL_([0-9]+)}/i',
			function ($matches) {
				$fID = $matches[1];
				if ($fID > 0) {
					return URL::to('/download_file', 'view', $fID);
				}
			},
			$text);

		return $text;
	}

	/**
	 * For the content block's getImportData() function
	 */
	public static function import($text) {
		$dom = new HtmlDomParser();
		$r = $dom->str_get_html($text);
		if (is_object($r)) {
			foreach($r->find('concrete-picture') as $picture) {
				$filename = $picture->file;
				$db = Loader::db();
				$fID = $db->GetOne('select fID from FileVersions where fvFilename = ?', array($filename));
				$picture->fID = $fID;
				$picture->file = false;
			}
			$text= (string) $r;
		}

		$text = preg_replace_callback(
			'/\{ccm:export:page:(.*?)\}/i',
			function ($matches) {
				$cPath = $matches[1];
				if ($cPath) {
					$pc = Page::getByPath($cPath);
					return '{CCM:CID_' . $pc->getCollectionID() . '}';
				} else {
					return '{CCM:CID_1}';
				}
			},
			$text);

		$text = preg_replace_callback(
			'/\{ccm:export:file:(.*?)\}/i',
			function ($matches) {
				$filename = $matches[1];
				$db = Loader::db();
				$fID = $db->GetOne('select fID from FileVersions where fvFilename = ?', array($filename));
				return '{CCM:FID_DL_' . $fID . '}';
			},
			$text);

		$text = preg_replace_callback(
			'/\{ccm:export:define:(.*?)\}/i',
			function ($matches) {
				$define = $matches[1];
				if (defined($define)) {
					$r = get_defined_constants();
					return $r[$define];
				}
			},
			$text);

		return $text;
	}

	/**
	 * For the content block's export() function
	 */
	public static function export($text) {
		$dom = new HtmlDomParser();
		$r = $dom->str_get_html($text);
		if (is_object($r)) {
			foreach($r->find('concrete-picture') as $picture) {
				$fID = $picture->fid;
				$f = \File::getByID($fID);
				if (is_object($f)) {
					$alt = $picture->alt;
					$style = $picture->style;
					$picture->fid = false;
					$picture->file = $f->getFilename();
				}
			}
			$text = (string)$r;
		}

		$text = preg_replace_callback(
			'/{CCM:CID_([0-9]+)}/i',
			array('\Concrete\Core\Backup\ContentExporter', 'replacePageWithPlaceHolderInMatch'),
			$text);

		$text = preg_replace_callback(
			'/{CCM:FID_DL_([0-9]+)}/i',
			array('\Concrete\Core\Backup\ContentExporter', 'replaceFileWithPlaceHolderInMatch'),
			$text);

		return $text;
	}
}
