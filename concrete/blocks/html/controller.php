<?php

namespace Concrete\Block\Html;

use Concrete\Core\Block\BlockController;
use Concrete\Core\Editor\LinkAbstractor;
use Concrete\Core\File\Tracker\FileTrackableInterface;
use Concrete\Core\File\Tracker\RichTextExtractor;

class Controller extends BlockController implements FileTrackableInterface
{
    public $content = '';

    protected $btTable = 'btContentLocal';

    protected $btInterfaceWidth = '600';

    protected $btWrapperClass = 'ccm-ui';

    protected $btInterfaceHeight = '500';

    protected $btCacheBlockRecord = true;

    protected $btCacheBlockOutput = true;

    protected $btCacheBlockOutputOnPost = true;

    protected $btCacheBlockOutputForRegisteredUsers = true;

    protected $btIgnorePageThemeGridFrameworkContainer = true;

    protected $btExportContentColumns = ['content'];

    public function getBlockTypeDescription()
    {
        return t('For adding HTML by hand.');
    }

    public function getBlockTypeName()
    {
        return t('HTML');
    }

    public function view()
    {
        $this->set('content', LinkAbstractor::translateFrom($this->content));
    }

    public function add()
    {
        $this->content = '';
        $this->edit();
    }

    public function edit()
    {
        $this->set('content', LinkAbstractor::translateFromEditMode($this->content));
        $this->requireAsset('ace');
    }

    public function getSearchableContent()
    {
        return $this->content;
    }

    public function save($data)
    {
        $args['content'] = LinkAbstractor::translateTo($data['content'] ?? '');
        parent::save($args);
    }

    public static function xml_highlight($s)
    {
        $s = htmlspecialchars($s);
        $s = preg_replace(
            "#&lt;([/]*?)(.*)([\s]*?)&gt;#sU",
            '<font color="#0000FF">&lt;\\1\\2\\3&gt;</font>',
            $s
        );
        $s = preg_replace(
            "#&lt;([\?])(.*)([\?])&gt;#sU",
            '<font color="#800000">&lt;\\1\\2\\3&gt;</font>',
            $s
        );
        $s = preg_replace(
            "#&lt;([^\s\?/=])(.*)([\[\s/]|&gt;)#iU",
            '&lt;<font color="#808000">\\1\\2</font>\\3',
            $s
        );
        $s = preg_replace(
            "#&lt;([/])([^\s]*?)([\s\]]*?)&gt;#iU",
            '&lt;\\1<font color="#808000">\\2</font>\\3&gt;',
            $s
        );
        $s = preg_replace(
            "#([^\s]*?)\=(&quot;|')(.*)(&quot;|')#isU",
            '<font color="#800080">\\1</font>=<font color="#FF00FF">\\2\\3\\4</font>',
            $s
        );
        $s = preg_replace(
            "#&lt;([^[]*+)(\[)([^]]*+)(\])&gt;#i",
            '&lt;\\1<font color="#800080">\\2\\3\\4</font>&gt;',
            $s
        );

        return nl2br($s);
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
}
