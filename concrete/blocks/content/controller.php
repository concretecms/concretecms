<?php

namespace Concrete\Block\Content;

use Concrete\Core\Block\BlockController;
use Concrete\Core\Editor\LinkAbstractor;
use Concrete\Core\Feature\Features;
use Concrete\Core\Feature\UsesFeatureInterface;
use Concrete\Core\File\Tracker\FileTrackableInterface;
use Concrete\Core\File\Tracker\RichTextExtractor;

/**
 * The controller for the content block.
 *
 * @package Blocks
 * @subpackage Content
 *
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2022 concreteCMS. (http://www.concretecms.org)
 * @license    http://www.concretecms.org/license/     MIT License
 */
class Controller extends BlockController implements FileTrackableInterface, UsesFeatureInterface
{
    /**
     * @var string
     */
    public $content;

    /**
     * @var string
     */
    protected $btTable = 'btContentLocal';

    /**
     * @var int
     */
    protected $btInterfaceWidth = 600;

    /**
     * @var int
     */
    protected $btInterfaceHeight = 465;

    /**
     * @var bool
     */
    protected $btCacheBlockRecord = true;

    /**
     * @var bool
     */
    protected $btCacheBlockOutput = true;

    /**
     * @var bool
     */
    protected $btCacheBlockOutputOnPost = true;

    /**
     * @var bool
     */
    protected $btSupportsInlineEdit = true;

    /**
     * @var bool
     */
    protected $btSupportsInlineAdd = true;

    /**
     * @var bool
     */
    protected $btCacheBlockOutputForRegisteredUsers = false;

    /**
     * @var int
     */
    protected $btCacheBlockOutputLifetime = 0; //until manually updated or cleared

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Block\BlockController::$btExportContentColumns
     */
    protected $btExportContentColumns = ['content'];

    /**
     * {@inhertdoc}.
     */
    public function getRequiredFeatures(): array
    {
        return [
            Features::IMAGERY,
        ];
    }

    /**
     * @return string
     */
    public function getBlockTypeDescription()
    {
        return t('HTML/WYSIWYG Editor Content.');
    }

    /**
     * @return string
     */
    public function getBlockTypeName()
    {
        return t('Content');
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return LinkAbstractor::translateFrom($this->content);
    }

    /**
     * @return string
     */
    public function getSearchableContent()
    {
        return $this->content;
    }

    /**
     * @param string $str
     *
     * @return array|string|string[]
     */
    public function br2nl($str)
    {
        return str_replace(["\r\n", "<br />\n", "<br />\r\n"], "\n", $str);
    }

    /**
     * @return void
     */
    public function view()
    {
        $this->set('content', $this->getContent());
    }

    /**
     * @return string
     */
    public function getContentEditMode()
    {
        return LinkAbstractor::translateFromEditMode($this->content);
    }

    /**
     * @param array<string,string> $args
     */
    public function save($args)
    {
        if (isset($args['content'])) {
            $args['content'] = LinkAbstractor::translateTo($args['content']);
        }
        parent::save($args);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\File\Tracker\FileTrackableInterface::getUsedFiles()
     */
    public function getUsedFiles()
    {
        return $this->app->make(RichTextExtractor::class)->extractFiles($this->content);
    }

    /**
     * @deprecated use \Concrete\Core\File\Tracker\RichTextExtractor
     */
    protected function getUsedFilesImages()
    {
        $files = [];
        $matches = [];
        if ($this->content && preg_match_all('/\<concrete-picture[^>]*?fID\s*=\s*[\'"]([^\'"]*?)[\'"]/i', $this->content, $matches)) {
            list(, $ids) = $matches;
            foreach ($ids as $id) {
                $files[] = $id;
            }
        }

        return $files;
    }

    /**
     * @deprecated use \Concrete\Core\File\Tracker\RichTextExtractor
     */
    protected function getUsedFilesDownload()
    {
        if (!$this->content) {
            return [];
        }
        preg_match_all('(FID_DL_\d+)', $this->content, $matches);

        return array_map(
            function ($match) {
                return explode('_', $match)[2];
            },
            $matches[0]
        );
    }
}
