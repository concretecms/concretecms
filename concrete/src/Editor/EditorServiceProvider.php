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
                'autogrow' => [t('Auto Grow'), t('The editor will automatically expand and shrink vertically depending on its content.')],
                // https://ckeditor.com/cke4/addon/a11yhelp
                'a11yhelp' => [t('Accessibility Help'), t('This plugin displays information about the keyboard usage using the ALT+0 combination.')],
                // https://ckeditor.com/cke4/addon/basicstyles
                'basicstyles' => [t('Basic Styles'), t('This plugin adds the basic formatting buttons: bold, italic, underline, strikethrough, subscript, superscript.')],
                // https://ckeditor.com/cke4/addon/bidi
                'bidi' => [t('BiDi (Text Direction)'), t('This plugin adds buttons to change the text direction.')],
                // https://ckeditor.com/cke4/addon/blockquote
                'blockquote' => [t('Blockquote'), t('This plugin adds a button to insert quotations (HTML <blockquote> tag).')],
                // https://ckeditor.com/cke4/addon/clipboard
                'clipboard' => [t('Clipboard'), t('This plugin handles the Cut/Copy/Paste operations.')],
                // https://ckeditor.com/cke4/addon/colorbutton
                'colorbutton' => [t('Color Button'), t('This plugin adds buttons to manage the text & background colors.')],
                // https://ckeditor.com/cke4/addon/colordialog
                'colordialog' => [t('Color Dialog'), t('This plugin improves the Color Button feature by providing an advanced color selection window.')],
                // https://ckeditor.com/cke4/addon/contextmenu
                'contextmenu' => [t('Context Menu'), t('The plugin replaces the browser\'s native menu with the editor\'s one.')],
                // https://ckeditor.com/cke4/addon/dialogadvtab
                'dialogadvtab' => [t('Advanced Tab for Dialogs'), t('This plugin provides the Advanced tab to extend some dialog windows.')],
                // https://ckeditor.com/cke4/addon/div
                'div' => [t('Div Container Manager'), t('This plugin adds a command that allows grouping content blocks under a container <div> element, with styles and attributes support.')],
                // https://ckeditor.com/cke4/addon/divarea
                'divarea' => [t('Div Editing Area'), t('This plugin uses a <div> element (instead of the <iframe> element) as the editable area. Much similar to inline editing, it allows the content to inherit styles from host page.')],
                // https://ckeditor.com/cke4/addon/elementspath
                'elementspath' => [t('Elements Path'), t('This plugin displays the list of HTML elements at the current cursor position.')],
                // https://ckeditor.com/cke4/addon/enterkey
                'enterkey' => [t('Enter Key'), t('This plugin defines the Enter key (line breaking) behavior.')],
                // https://ckeditor.com/cke4/addon/entities
                'entities' => [t('Escape HTML Entities'), t('This plugin escapes entities in the generated HTML.')],
                // https://ckeditor.com/cke4/addon/find
                'find' => [t('Find / Replace'), t('This plugin adds the Find and Replace dialog.')],
                // https://ckeditor.com/cke4/addon/flash
                'flash' => [t('Flash Dialog'), t('This plugin adds features to manage embedded Flash files.')],
                // https://ckeditor.com/cke4/addon/floatingspace
                'floatingspace' => [t('Floating Space'), t('This plugin places the editor toolbar in the best position.')],
                // https://ckeditor.com/cke4/addon/font
                'font' => [t('Font Size and Family'), t('This plugin adds the Font Size and Font Family dropdowns.')],
                // https://ckeditor.com/cke4/addon/format
                'format' => [t('Format'), t('This plugin adds the Format dropdown.')],
                // https://ckeditor.com/cke4/addon/horizontalrule
                'horizontalrule' => [t('Horizontal Rule'), t('This plugin adds a button to insert Horizontal Rules (<hr>).')],
                // https://ckeditor.com/cke4/addon/htmlwriter
                'htmlwriter' => [t('HTML Output Writer'), t('This plugin provides flexible HTML output formatting.')],
                // https://ckeditor.com/cke4/addon/image
                'image' => [t('Image'), t('This plugin adds the image-related features.')],
                // https://ckeditor.com/cke4/addon/image2
                'image2' => [t('Enhanced Image'), t('This is an enhanced version of the Image plugin that introduces the image widget.')],
                // https://ckeditor.com/cke4/addon/indentblock
                'indentblock' => [t('Indent Block'), t('This plugin adds buttons to manage the text indentation.')],
                // https://ckeditor.com/cke4/addon/indentlist
                'indentlist' => [t('Indent List'), t('This plugin adds buttons to manage the lists (<ul> and <ol> elements) indentation.')],
                // https://ckeditor.com/cke4/addon/justify
                'justify' => [t('Justify'), t('This plugin adds text justification buttons.')],
                // https://ckeditor.com/cke4/addon/language
                'language' => [t('Language'), t('This plugin adds the Language button.')],
                // https://ckeditor.com/cke4/addon/list
                'list' => [t('List'), t('This plugin adds buttons to add/remove numbered and bulleted lists.')],
                // https://ckeditor.com/cke4/addon/liststyle
                'liststyle' => [t('List Style'), t('This plugin adds numbered list and ordered list properties dialogs (available in context menu).')],
                // https://ckeditor.com/cke4/addon/magicline
                'magicline' => [t('Magic Line'), t('This plugin makes it easier to place cursor and add content near some problematic document elements.')],
                // https://ckeditor.com/cke4/addon/maximize
                'maximize' => [t('Maximize'), t('This plugin adds a button to maximize the editor.')],
                // https://ckeditor.com/cke4/addon/newpage
                'newpage' => [t('New Page'), t('This plugin adds a button to clears the editor content.')],
                // https://ckeditor.com/cke4/addon/pagebreak
                'pagebreak' => [t('Page Break'), t('This plugin adds a button which inserts horizontal page breaks (useful for document printing).')],
                // https://ckeditor.com/cke4/addon/pastetext
                'pastetext' => [t('Paste As Plain Text'), t('This adds a button to paste clipboard contents as plain text.')],
                // https://ckeditor.com/cke4/addon/pastefromword
                'pastefromword' => [t('Paste from Word'), t('This adds a button to paste content from Microsoft Word and maintain original formatting.')],
                // https://ckeditor.com/cke4/addon/preview
                'preview' => [t('Preview'), t('This plugin adds a button which shows a preview of the document as it will be displayed to end users or printed.')],
                // https://ckeditor.com/cke4/addon/removeformat
                'removeformat' => [t('Remove Format'), t('This plugin adds the Remove Format button to remove all styles from the selected part of the document.')],
                // https://ckeditor.com/cke4/addon/resize
                'resize' => [t('Editor Resize'), t('This plugin adds a handle that allows resizing the classic editor instances.')],
                // https://ckeditor.com/cke4/addon/scayt
                'scayt' => [t('SpellCheckAsYouType (SCAYT)'), t('This plugin brings Spell Check As You Type (SCAYT) functionality.')],
                // https://ckeditor.com/cke4/addon/selectall
                'selectall' => [t('Select All'), t('This plugin adds a Select all button.')],
                // https://ckeditor.com/cke4/addon/showblocks
                'showblocks' => [t('Show Blocks'), t('This plugin adds a button to outline all block-level elements.')],
                // https://ckeditor.com/cke4/addon/showborders
                'showborders' => [t('Show Table Borders'), t('This plugin displays visible outlines around all table elements.')],
                // https://ckeditor.com/cke4/addon/smiley
                'smiley' => [t('Insert Smiley'), t('This plugin adds a button to insert emoticons.')],
                // https://ckeditor.com/cke4/addon/sourcearea
                'sourcearea' => [t('Source Editing Area'), t('This plugin adds a button to switch to the HTML source editing mode.')],
                // https://ckeditor.com/cke4/addon/sourcedialog
                'sourcedialog' => [t('Source Dialog'), t('This plugin adds a button to edit the HTML source in a dialog window.')],
                // https://ckeditor.com/cke4/addon/specialchar
                'specialchar' => [t('Special Characters'), t('This plugin adds a button to insert characters.')],
                // https://ckeditor.com/cke4/addon/stylescombo
                'stylescombo' => [t('Styles Combo'), t('This plugin adds the Styles dropdown.')],
                // https://ckeditor.com/cke4/addon/tab
                'tab' => [t('Tab Key Handling'), t('This plugin provides basic TAB/SHIFT-TAB key handling (move to next/previous table cell, move to next editor instance in page).')],
                // https://ckeditor.com/cke4/addon/table
                'table' => [t('Table'), t('This plugin adds the Table Properties dialog window to create and edit tables.')],
                // https://ckeditor.com/cke4/addon/tableresize
                'tableresize' => [t('Table Resize'), t('This plugin adds support for table column resizing with the mouse.')],
                // https://ckeditor.com/cke4/addon/tableselection
                'tableselection' => [t('Table Selection'), t('This plugin allows selecting arbitrary rectangular table fragments, appling formatting or adding links to all selected cells, cut/copy/paste entire rows or columns.')],
                // https://ckeditor.com/cke4/addon/tabletools
                'tabletools' => [t('Table Tools'), t('This plugin adds a more advanced context menu for table items and the Cell Properties dialog window.')],
                // https://ckeditor.com/cke4/addon/toolbar
                'toolbar' => [t('Editor Toolbar'), t('This plugin provides the classical experience to access editor commands, including items like buttons and drop-down lists.')],
                // https://ckeditor.com/cke4/addon/undo
                'undo' => [t('Undo'), t('This plugin provides the undo and redo features.')],
                // https://ckeditor.com/cke4/addon/wsc
                'wsc' => [t('WebSpellChecker'), t('This plugin provides a dialog window for spell checking.')],
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
