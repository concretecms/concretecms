<?php
namespace Concrete\Block\Content;

use Concrete\Core\Backup\ContentImporter\ValueInspector\InspectionRoutine\PictureRoutine;
use Concrete\Core\Block\BlockController;
use Concrete\Core\Editor\LinkAbstractor;
use Concrete\Core\File\Tracker\FileTrackableInterface;
use Concrete\Core\Statistics\UsageTracker\AggregateTracker;

/**
 * The controller for the content block.
 *
 * @package Blocks
 * @subpackage Content
 *
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2012 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */
class Controller extends BlockController implements FileTrackableInterface
{
    protected $btTable = 'btContentLocal';
    protected $btInterfaceWidth = "600";
    protected $btInterfaceHeight = "465";
    protected $btCacheBlockRecord = true;
    protected $btCacheBlockOutput = true;
    protected $btCacheBlockOutputOnPost = true;
    protected $btSupportsInlineEdit = true;
    protected $btSupportsInlineAdd = true;
    protected $btCacheBlockOutputForRegisteredUsers = false;
    protected $btCacheBlockOutputLifetime = 0; //until manually updated or cleared

    public $content;

    /**
     * @var \Concrete\Core\Statistics\UsageTracker\AggregateTracker
     */
    protected $tracker;

    public function getBlockTypeDescription()
    {
        return t("HTML/WYSIWYG Editor Content.");
    }

    public function getBlockTypeName()
    {
        return t("Content");
    }

    public function __construct($obj=null, AggregateTracker $tracker=null)
    {
        parent::__construct($obj);
        $this->tracker = $tracker;
    }

    public function getContent()
    {
        return LinkAbstractor::translateFrom($this->content);
    }

    public function getSearchableContent()
    {
        return $this->content;
    }

    public function br2nl($str)
    {
        $str = str_replace("\r\n", "\n", $str);
        $str = str_replace("<br />\n", "\n", $str);

        return $str;
    }

    public function registerViewAssets($outputContent = '')
    {
        if (preg_match('/data-concrete5-link-lightbox/i', $outputContent)) {
            $this->requireAsset('core/lightbox');
        }
    }

    public function view()
    {
        $this->set('content', $this->getContent());
    }

    public function getContentEditMode()
    {
        return LinkAbstractor::translateFromEditMode($this->content);
    }

    public function getImportData($blockNode, $page)
    {
        $content = $blockNode->data->record->content;
        $content = LinkAbstractor::import($content);
        $args = array('content' => $content);

        return $args;
    }

    public function export(\SimpleXMLElement $blockNode)
    {
        $data = $blockNode->addChild('data');
        $data->addAttribute('table', $this->btTable);
        $record = $data->addChild('record');
        $cnode = $record->addChild('content');
        $node = dom_import_simplexml($cnode);
        $no = $node->ownerDocument;
        $content = LinkAbstractor::export($this->content);
        $cdata = $no->createCDataSection($content);
        $node->appendChild($cdata);
    }

    public function save($args)
    {
        if (isset($args['content'])) {
            $args['content'] = LinkAbstractor::translateTo($args['content']);
        }
        parent::save($args);
        $this->tracker->track($this);
    }

    /**
     * Tell the tracker to forget us when we are deleted
     */
    public function delete()
    {
        parent::delete();
        $this->tracker->forget($this);
    }

    public function getUsedFiles()
    {
        $files = [];
        $matches = [];
        if (preg_match_all('/\<concrete-picture[^>]*?fID\s*=\s*[\'"]([^\'"]*?)[\'"]/i', $this->content, $matches)) {
            list(,$ids) = $matches;
            foreach ($ids as $id) {
                $files[] = intval($id);
            }
        }

        return $files;
    }

    public function getUsedCollection()
    {
        return $this->getCollectionObject();
    }

}
