<?

/**
 * Routes file types to importers, handlers.
 * File type icons provided by http://Jordan-Michael.com/
 * @package Files
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2009 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */

defined('C5_EXECUTE') or die("Access Denied.");

$ft = FileTypeList::getInstance();
$ft->define('jpg,jpeg,jpe', t('JPEG'), FileType::T_IMAGE, 'image', 'image', 'image');
$ft->define('gif', t('GIF'), FileType::T_IMAGE, 'image', 'image', 'image');
$ft->define('png', t('PNG'), FileType::T_IMAGE, 'image', 'image', 'image');
$ft->define('bmp', t('Windows Bitmap'), FileType::T_IMAGE, 'image');
$ft->define('tif,tiff', t('TIFF'), FileType::T_IMAGE, 'image');
$ft->define('htm,html', t('HTML'), FileType::T_IMAGE);
$ft->define('swf', t('Flash'), FileType::T_IMAGE, 'image');
$ft->define('ico', t('Icon'), FileType::T_IMAGE);
$ft->define('svg', t('SVG'), FileType::T_IMAGE);
$ft->define('asf,wmv', t('Windows Video'), FileType::T_VIDEO, false, 'video');
$ft->define('mov,qt', t('Quicktime'), FileType::T_VIDEO, false, 'video');
$ft->define('avi', t('AVI'), FileType::T_VIDEO, false, 'video');
$ft->define('3gp', t('3GP'), FileType::T_VIDEO, false, 'video');
$ft->define('txt', t('Plain Text'), FileType::T_TEXT, false, 'text');
$ft->define('csv', t('CSV'), FileType::T_TEXT, false, 'text');
$ft->define('xml', t('XML'), FileType::T_TEXT);
$ft->define('php', t('PHP'), FileType::T_TEXT);
$ft->define('doc,docx', t('MS Word'), FileType::T_DOCUMENT);
$ft->define('css', t('Stylesheet'), FileType::T_TEXT);
$ft->define('mp4', t('MP4'), FileType::T_VIDEO);
$ft->define('flv', t('FLV'), FileType::T_VIDEO, 'flv');
$ft->define('mp3', t('MP3'), FileType::T_AUDIO, false, 'audio');
$ft->define('m4a', t('MP4'), FileType::T_AUDIO, false, 'audio');
$ft->define('ra,ram', t('Realaudio'), FileType::T_AUDIO);
$ft->define('wma', t('Windows Audio'), FileType::T_AUDIO);
$ft->define('rtf', t('Rich Text'), FileType::T_DOCUMENT);
$ft->define('js', t('JavaScript'), FileType::T_TEXT);
$ft->define('pdf', t('PDF'), FileType::T_DOCUMENT);
$ft->define('psd', t('Photoshop'), FileType::T_IMAGE);
$ft->define('mpeg,mpg', t('MPEG'), FileType::T_VIDEO);
$ft->define('xla,xls,xlsx,xlt,xlw', t('MS Excel'), FileType::T_DOCUMENT);
$ft->define('pps,ppt,pptx,pot', t('MS Powerpoint'), FileType::T_DOCUMENT);
$ft->define('tar', t('TAR Archive'), FileType::T_APPLICATION);
$ft->define('zip', t('Zip Archive'), FileType::T_APPLICATION);
$ft->define('gz,gzip', t('GZip Archive'), FileType::T_APPLICATION);
$ft->define('ogg', t('OGG'), FileType::T_AUDIO);
$ft->define('ogv', t('OGG Video'), FileType::T_VIDEO);
$ft->define('webm', t('WebM'), FileType::T_VIDEO);

$ft->defineImporterAttribute('width', 'Width', 'NUMBER', false);
$ft->defineImporterAttribute('height', 'Height', 'NUMBER', false);
$ft->defineImporterAttribute('duration', 'Duration', 'NUMBER', false);
