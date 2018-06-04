<?php

namespace Concrete\Core\Editor;

use AssetList;
use Concrete\Core\Application\Application;
use Concrete\Core\Foundation\Service\Provider as ServiceProvider;
use Concrete\Core\Legacy\FilePermissions;
use Concrete\Core\Legacy\TaskPermission;
use Concrete\Core\Localization\Localization;

class EditorServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(
            EditorInterface::class,
            function (Application $app) {
                $config = $app->make('site')->getSite()->getConfigRepository();
                $styles = $config->get('editor.ckeditor4.styles', []);
                $pluginManager = new PluginManager();
                $selectedPlugins = $config->get('editor.ckeditor4.plugins.selected');
                if (!is_array($selectedPlugins)) {
                    $selectedPlugins = array_merge($config->get('editor.ckeditor4.plugins.selected_default'), $config->get('editor.ckeditor4.plugins.selected_hidden'));
                }
                $pluginManager->select($selectedPlugins);
                $this->registerCkeditorPlugins($pluginManager);
                $this->registerCorePlugins($pluginManager);
                $editor = new CkeditorEditor($config, $pluginManager, $styles);
                $editor->setToken($app->make('token')->generate('editor'));

                $filePermission = FilePermissions::getGlobal();
                $taskPermission = new TaskPermission();

                $editor->setAllowFileManager(
                    $filePermission->canAccessFileManager()
                    && $config->get('editor.concrete.enable_filemanager')
                );
                $editor->setAllowSitemap(
                    $taskPermission->canAccessSitemap()
                    && $config->get('editor.concrete.enable_sitemap')
                );

                return $editor;
            }
        );
        $this->app->alias(EditorInterface::class, 'editor');
    }

    protected function registerCkeditorPlugins(PluginManager $pluginManager)
    {
        $loc = Localization::getInstance();
        $loc->pushActiveContext(Localization::CONTEXT_UI);
        try {
            $pluginList = [
                // https://ckeditor.com/cke4/addon/about
                'about' => [t('About'), t('This plugin displays the editor version, online documentation links, and licensing information.')],
                // https://ckeditor.com/cke4/addon/autogrow
                'autogrow' => [t('Auto Grow'), t('The editor will automatically expand and shrink vertically depending on the amount and size of content entered in its editing area.')],
                // https://ckeditor.com/cke4/addon/a11yhelp
                'a11yhelp' => [t('Accessibility Help'), t('This plugin displays the editor key map information in a dialog window to illustrate generic keystrokes to access each editor part, as well as the key bindings to various editor commands.')],
                // https://ckeditor.com/cke4/addon/basicstyles
                'basicstyles' => [t('Basic Styles'), t('This plugin adds the following basic formatting commands to the editor: bold, italic, underline, strikethrough, subscript, superscript.')],
                // https://ckeditor.com/cke4/addon/bidi
                'bidi' => [t('BiDi (Text Direction)'), t('This plugin makes it possible to change the text direction for a block-level content element.')],
                // https://ckeditor.com/cke4/addon/blockquote
                'blockquote' => [t('Blockquote'), t('This plugin provides the feature of block-level quotation (HTML <blockquote> tag) in contents.')],
                // https://ckeditor.com/cke4/addon/clipboard
                'clipboard' => [t('Clipboard'), t('This plugin handles cut/copy/paste inside of the editor.')],
                // https://ckeditor.com/cke4/addon/colorbutton
                'colorbutton' => [t('Color Button'), t('This plugin adds the Text Color and Background Color feature to the editor.')],
                // https://ckeditor.com/cke4/addon/colordialog
                'colordialog' => [t('Color Dialog'), t('This plugin provides a dedicated Select Color dialog window with a table of colors.')],
                // https://ckeditor.com/cke4/addon/contextmenu
                'contextmenu' => [t('Context Menu'), t('The plugin provides the editor\'s context menu to use instead of the browser\'s native menu in place.')],
                // https://ckeditor.com/cke4/addon/dialogadvtab
                'dialogadvtab' => [t('Advanced Tab for Dialogs'), t('This plugin provides the Advanced dialog window tab to extend some editor dialog windows.')],
                // https://ckeditor.com/cke4/addon/div
                'div' => [t('Div Container Manager'), t('This plugin adds a command that allows for grouping of content blocks under a <div> element as a container, with styles and attributes optionally specified in a dedicated dialog.')],
                // https://ckeditor.com/cke4/addon/divarea
                'divarea' => [t('Div Editing Area'), t('This plugin uses a <div> element (instead of the traditional <iframe> element) as the editable area in the themedui creator. Much similar to inline editing, it benefits from allowing the editor content to inherit from host page styles.')],
                // https://ckeditor.com/cke4/addon/elementspath
                'elementspath' => [t('Elements Path'), t('This plugin displays a list of HTML element names in the current cursor position.')],
                // https://ckeditor.com/cke4/addon/enterkey
                'enterkey' => [t('Enter Key'), t('This plugin defines the Enter key (line breaking) behavior in all contexts.')],
                // https://ckeditor.com/cke4/addon/entities
                'entities' => [t('Escape HTML Entities'), t('This plugin escapes HTML entities that appear in the editor output based on configuration.')],
                // https://ckeditor.com/cke4/addon/find
                'find' => [t('Find / Replace'), t('This plugin adds Find and Replace dialog.')],
                // https://ckeditor.com/cke4/addon/flash
                'flash' => [t('Flash Dialog'), t('This plugin comes with a dialog to manage flash embed in the document.')],
                // https://ckeditor.com/cke4/addon/floatingspace
                'floatingspace' => [t('Floating Space'), t('This plugin is used in inline creator to place the editor toolbar in the best position around the editable element.')],
                // https://ckeditor.com/cke4/addon/font
                'font' => [t('Font Size and Family'), t('This plugin adds Font Size and Font Family dropdowns that applies as inline element style.')],
                // https://ckeditor.com/cke4/addon/format
                'format' => [t('Format'), t('This plugin adds Format combo allows you to apply block-level format styles.')],
                // https://ckeditor.com/cke4/addon/horizontalrule
                'horizontalrule' => [t('Horizontal Rule'), t('This plugin provides the command to insert Horizontal Rule (<hr> element) in content.')],
                // https://ckeditor.com/cke4/addon/htmlwriter
                'htmlwriter' => [t('HTML Output Writer'), t('This plugin provides flexible HTML output formatting.')],
                // https://ckeditor.com/cke4/addon/image
                'image' => [t('Image'), t('This plugin adds the image-related features to the editor.')],
                // https://ckeditor.com/cke4/addon/image2
                'image2' => [t('Enhanced Image'), t('This is an enhanced version of the Image plugin that introduces the image widget.')],
                // https://ckeditor.com/cke4/addon/indentblock
                'indentblock' => [t('Indent Block'), t('This plugin handles indentation of text blocks.')],
                // https://ckeditor.com/cke4/addon/indentlist
                'indentlist' => [t('Indent List'), t('This plugin handles list indentation (for <ul> and <ol> elements).')],
                // https://ckeditor.com/cke4/addon/justify
                'justify' => [t('Justify'), t('This plugin adds text justification commands.')],
                // https://ckeditor.com/cke4/addon/language
                'language' => [t('Language'), t('This plugin implements the Language toolbar button.')],
                // https://ckeditor.com/cke4/addon/list
                'list' => [t('List'), t('With this plugin it is possible to insert and remove numbered and bulleted lists in the editor.')],
                // https://ckeditor.com/cke4/addon/liststyle
                'liststyle' => [t('List Style'), t('This plugin adds numbered list and ordered list properties dialogs (available in context menu).')],
                // https://ckeditor.com/cke4/addon/magicline
                'magicline' => [t('Magic Line'), t('This plugin makes it easier to place cursor and add content near some problematic document elements.')],
                // https://ckeditor.com/cke4/addon/maximize
                'maximize' => [t('Maximize'), t('This plugin adds toolbar button maximizing the editor inside a browser window.')],
                // https://ckeditor.com/cke4/addon/newpage
                'newpage' => [t('New Page'), t('This plugin adds toolbar button which clears the editing area and creates a new page.')],
                // https://ckeditor.com/cke4/addon/pagebreak
                'pagebreak' => [t('Page Break'), t('This plugin adds toolbar button which inserts horizontal page breaks, useful for setting document printing sections.')],
                // https://ckeditor.com/cke4/addon/pastetext
                'pastetext' => [t('Paste As Plain Text'), t('With this plugin it is possible to have the clipboard data to be always pasted as plain text.')],
                // https://ckeditor.com/cke4/addon/pastefromword
                'pastefromword' => [t('Paste from Word'), t('This plugin allows you to paste content from Microsoft Word and maintain original content formatting.')],
                // https://ckeditor.com/cke4/addon/preview
                'preview' => [t('Preview'), t('This plugin adds toolbar button which shows a preview of the document as it will be displayed to end users or printed.')],
                // https://ckeditor.com/cke4/addon/removeformat
                'removeformat' => [t('Remove Format'), t('This plugin adds the Remove Format toolbar button which makes it possible to remove all text styling (bold, font color, etc.) applied to a selected part of the document.')],
                // https://ckeditor.com/cke4/addon/resize
                'resize' => [t('Editor Resize'), t('This plugin allows you to resize the classic editor instance by dragging the resize handle located in the bottom corner of the editor.')],
                // https://ckeditor.com/cke4/addon/scayt
                'scayt' => [t('SpellCheckAsYouType (SCAYT)'), t('This plugin brings Spell Check As You Type (SCAYT) functionality.')],
                // https://ckeditor.com/cke4/addon/selectall
                'selectall' => [t('Select All'), t('This plugin adds select all toolbar button which makes possible to select entire contents of the document.')],
                // https://ckeditor.com/cke4/addon/showblocks
                'showblocks' => [t('Show Blocks'), t('This plugin adds the command to visualize all block-level elements by surrounding them with an outline and displaying their element name at the top-left.')],
                // https://ckeditor.com/cke4/addon/showborders
                'showborders' => [t('Show Table Borders'), t('This plugin displays visible outlines around all table elements.')],
                // https://ckeditor.com/cke4/addon/smiley
                'smiley' => [t('Insert Smiley'), t('This plugin provides a set of emoticons to insert into the editor via a dialog window.')],
                // https://ckeditor.com/cke4/addon/sourcearea
                'sourcearea' => [t('Source Editing Area'), t('This plugin adds the source editing mode.')],
                // https://ckeditor.com/cke4/addon/sourcedialog
                'sourcedialog' => [t('Source Dialog'), t('This plugin provides an easy way to edit raw HTML source of the editor content using a dialog window.')],
                // https://ckeditor.com/cke4/addon/specialchar
                'specialchar' => [t('Special Characters'), t('With this plugin it is possible to insert characters that are not part of the standard keyboard.')],
                // https://ckeditor.com/cke4/addon/stylescombo
                'stylescombo' => [t('Styles Combo'), t('This plugin provides the Styles drop-down list added to the editor toolbar.')],
                // https://ckeditor.com/cke4/addon/tab
                'tab' => [t('Tab Key Handling'), t('This plugin provides basic Tab/Shift-Tab key handling (move to next/previous table cell, move to next editor instance in page).')],
                // https://ckeditor.com/cke4/addon/table
                'table' => [t('Table'), t('This plugin adds the Table Properties dialog window with support for creating tables and setting basic table properties.')],
                // https://ckeditor.com/cke4/addon/tableresize
                'tableresize' => [t('Table Resize'), t('This plugin adds support for table column resizing with the mouse.')],
                // https://ckeditor.com/cke4/addon/tableselection
                'tableselection' => [t('Table Selection'), t('This plugin introduces a unique custom selection system for tables to select arbitrary rectangular table fragments, apply formatting or add a link to all selected cells at once, cut/copy/paste entire rows or columns.')],
                // https://ckeditor.com/cke4/addon/tabletools
                'tabletools' => [t('Table Tools'), t('This plugin adds a more advanced context menu for table items and the Cell Properties dialog window.')],
                // https://ckeditor.com/cke4/addon/toolbar
                'toolbar' => [t('Editor Toolbar'), t('This plugin provides the classical experience to access editor commands, including items like buttons and drop-down lists.')],
                // https://ckeditor.com/cke4/addon/undo
                'undo' => [t('Undo'), t('This plugin is to provide undo and redo feature to content modifications.')],
                // https://ckeditor.com/cke4/addon/wsc
                'wsc' => [t('WebSpellChecker'), t('This plugin brings spell checking in a dialog window into the editor.')],
                // https://ckeditor.com/cke4/addon/wysiwygarea
                'wysiwygarea' => [t('IFrame Editing Area'), t('This plugin represents an editing area that stores the editor content inside of an embedded iframe.')],
            ];
        } finally {
            $loc->popActiveContext();
        }
        foreach ($pluginList as $key => list($name, $description)) {
            $editorPlugin = new Plugin();
            $editorPlugin->setKey($key);
            $editorPlugin->setName($name);
            $editorPlugin->setDescription($description);
            $pluginManager->register($editorPlugin);
        }
    }

    private function registerCorePlugins(PluginManager $pluginManager)
    {
        $coreAssetDir = 'js/ckeditor4/core/';
        $vendorAssetDir = 'js/ckeditor4/vendor/';

        $assetList = AssetList::getInstance();
        $assetList->register(
            'javascript',
            'editor/ckeditor4',
            $vendorAssetDir . 'ckeditor.js',
            ['combine' => false, 'minify' => false]
        );
        $assetList->register(
            'css',
            'editor/ckeditor4',
            $coreAssetDir . 'ckeditor.css'
        );
        $assetList->register(
            'javascript',
            'editor/ckeditor4/jquery_adapter',
            $vendorAssetDir . 'adapters/jquery.js'
        );

        $assetList->registerGroup(
            'editor/ckeditor4',
            [
                ['javascript', 'editor/ckeditor4'],
                ['css', 'editor/ckeditor4'],
                ['javascript', 'editor/ckeditor4/jquery_adapter'],
            ]
        );

        $assetList->register(
            'javascript',
            'editor/ckeditor4/concrete5inline',
            $coreAssetDir . 'concrete5inline/register.js'
        );
        $assetList->register(
            'css',
            'editor/ckeditor4/concrete5inline',
            $coreAssetDir . 'concrete5inline/styles.css'
        );

        $assetList->registerGroup(
            'editor/ckeditor4/concrete5inline',
            [
                ['javascript', 'editor/ckeditor4/concrete5inline'],
                ['css', 'editor/ckeditor4/concrete5inline'],
            ]
        );

        $assetList->register(
            'javascript',
            'editor/ckeditor4/concrete5filemanager',
            $coreAssetDir . 'concrete5filemanager/register.js'
        );
        $assetList->register(
            'css',
            'editor/ckeditor4/concrete5filemanager',
            $coreAssetDir . 'concrete5filemanager/styles.css'
        );
        $assetList->registerGroup(
            'editor/ckeditor4/concrete5filemanager',
            [
                ['javascript', 'editor/ckeditor4/concrete5filemanager'],
                ['css', 'editor/ckeditor4/concrete5filemanager'],
            ]
        );

        $assetList->register(
            'javascript',
            'editor/ckeditor4/concrete5uploadimage',
            $coreAssetDir . 'concrete5uploadimage/register.js'
        );
        $assetList->registerGroup(
            'editor/ckeditor4/concrete5uploadimage',
            [
                ['javascript', 'editor/ckeditor4/concrete5uploadimage'],
            ]
        );

        $assetList->register(
            'javascript',
            'editor/ckeditor4/concrete5link',
            $coreAssetDir . 'concrete5link/register.js'
        );
        $assetList->registerGroup(
            'editor/ckeditor4/concrete5link',
            [
                ['javascript', 'editor/ckeditor4/concrete5link'],
            ]
        );

        $assetList->register(
            'javascript',
            'editor/ckeditor4/normalizeonchange',
            $coreAssetDir . 'normalizeonchange/register.js'
        );
        $assetList->registerGroup(
            'editor/ckeditor4/normalizeonchange',
            [
                ['javascript', 'editor/ckeditor4/normalizeonchange'],
            ]
        );

        $assetList->register(
            'javascript',
            'editor/ckeditor4/concrete5styles',
            $coreAssetDir . 'concrete5styles/register.js'
        );
        $assetList->register(
            'css',
            'editor/ckeditor4/concrete5styles',
            $coreAssetDir . 'concrete5styles/styles.css'
        );
        $assetList->registerGroup(
            'editor/ckeditor4/concrete5styles',
            [
                ['javascript', 'editor/ckeditor4/concrete5styles'],
                ['css', 'editor/ckeditor4/concrete5styles'],
            ]
        );

        $plugin = new Plugin();
        $plugin->setKey('concrete5inline');
        $plugin->setName(t('concrete5 Inline'));
        $plugin->requireAsset('editor/ckeditor4/concrete5inline');
        $pluginManager->register($plugin);

        $plugin = new Plugin();
        $plugin->setKey('concrete5filemanager');
        $plugin->setName(t('concrete5 File Browser'));
        $plugin->requireAsset('editor/ckeditor4/concrete5filemanager');
        $pluginManager->register($plugin);

        $plugin = new Plugin();
        $plugin->setKey('concrete5uploadimage');
        $plugin->setName(t('concrete5 Upload Image'));
        $plugin->requireAsset('editor/ckeditor4/concrete5uploadimage');
        $pluginManager->register($plugin);

        $plugin = new Plugin();
        $plugin->setKey('concrete5link');
        $plugin->setName(t('concrete5 Link'));
        $plugin->requireAsset('editor/ckeditor4/concrete5link');
        $pluginManager->register($plugin);

        $plugin = new Plugin();
        $plugin->setKey('normalizeonchange');
        $plugin->setName(t('Normalize On Change'));
        $plugin->requireAsset('editor/ckeditor4/normalizeonchange');
        $pluginManager->register($plugin);

        $plugin = new Plugin();
        $plugin->setKey('concrete5styles');
        $plugin->setName(t('concrete5 Styles'));
        $plugin->requireAsset('editor/ckeditor4/concrete5styles');
        $pluginManager->register($plugin);
    }
}
