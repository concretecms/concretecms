<?php

namespace Concrete\Core\Editor;

use AssetList;
use Concrete\Core\Application\Application;
use Concrete\Core\Config\Repository\Liaison;
use Concrete\Core\Entity\Site\Site;
use Concrete\Core\Foundation\Service\Provider as ServiceProvider;
use Concrete\Core\Legacy\FilePermissions;
use Concrete\Core\Legacy\TaskPermission;
use Concrete\Core\Localization\Localization;
use Concrete\Core\Site\Service;

class EditorServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(
            CkeditorEditor::class,
            function (Application $app) {
                $siteService = $app->make('site');
                $activeSite = $siteService->getActiveSiteForEditing();
                $config = $activeSite->getConfigRepository();

                $styles = $config->get('editor.ckeditor4.styles', []);

                // Load plugins and select the site specific ones
                $pluginManager = new PluginManager();
                $this->registerCkeditorPlugins($pluginManager);
                $this->registerCorePlugins($pluginManager);
                $pluginManager->select($this->resolveSelectedPlugins($activeSite, $config, $siteService));

                $editor = new CkeditorEditor($config, $siteService, $pluginManager, $styles, $app);
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
        $this->app->alias(CkeditorEditor::class, EditorInterface::class);
        $this->app->alias(CkeditorEditor::class, 'editor');
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
                // https://ckeditor.com/cke4/addon/autolink
                'autolink' => [t('Auto Link'), t('This plugin turns pasted URL text into links. The URL text must include the protocol type such as HTTP and HTTPS.')],
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
                // https://ckeditor.com/cke4/addon/emoji
                'emoji' => [t('Emoji'), t('This plugin adds autocomplete for inserting Unicode emoji characters. Typing a colon ( : ) followed by at least two additional characters will list available emojis.')],
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
                // https://ckeditor.com/cke4/addon/placeholder
                'placeholder' => [t('Placeholder'), t('This plugin lets you create and edit placeholders (non-editable text fragments).')],
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
                'tableselection' => [t('Table Selection'), t('This plugin allows selecting arbitrary rectangular table fragments, applying formatting or adding links to all selected cells, cut/copy/paste entire rows or columns.')],
                // https://ckeditor.com/cke4/addon/tabletools
                'tabletools' => [t('Table Tools'), t('This plugin adds a more advanced context menu for table items and the Cell Properties dialog window.')],
                // https://ckeditor.com/cke4/addon/toolbar
                'toolbar' => [t('Editor Toolbar'), t('This plugin provides the classical experience to access editor commands, including items like buttons and drop-down lists.')],
                // https://ckeditor.com/cke4/addon/undo
                'undo' => [t('Undo'), t('This plugin provides the undo and redo features.')],
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
        $plugin = new Plugin();
        $plugin->setKey('concreteinline');
        $plugin->setName(t('Concrete Inline'));
        $plugin->requireAsset('ckeditor');
        $pluginManager->register($plugin);

        $plugin = new Plugin();
        $plugin->setKey('concretefilemanager');
        $plugin->setName(t('Concrete File Browser'));
        $plugin->requireAsset('ckeditor');
        $pluginManager->register($plugin);

        $plugin = new Plugin();
        $plugin->setKey('concreteuploadimage');
        $plugin->setName(t('Concrete Upload Image'));
        $plugin->requireAsset('ckeditor');
        $pluginManager->register($plugin);

        $plugin = new Plugin();
        $plugin->setKey('concretelink');
        $plugin->setName(t('Concrete Link'));
        $plugin->requireAsset('ckeditor');
        $pluginManager->register($plugin);

        $plugin = new Plugin();
        $plugin->setKey('normalizeonchange');
        $plugin->setName(t('Normalize On Change'));
        $plugin->requireAsset('ckeditor');
        $pluginManager->register($plugin);

        $plugin = new Plugin();
        $plugin->setKey('concretestyles');
        $plugin->setName(t('Concrete Styles'));
        $plugin->requireAsset('ckeditor');
        $pluginManager->register($plugin);
    }

    /**
     * Find the selected ckeditor plugins
     *
     * @param \Concrete\Core\Entity\Site\Site $activeSite
     * @param \Concrete\Core\Config\Repository\Liaison $config
     * @param \Concrete\Core\Site\Service $siteService
     *
     * @return array
     */
    protected function resolveSelectedPlugins(Site $activeSite, Liaison $config, Service $siteService)
    {
        // Load the selected plugins from the current site
        $selectedPlugins = $config->get('editor.ckeditor4.plugins.selected');

        if (!is_array($selectedPlugins)) {
            // Resolve the default config to use
            if ($activeSite->getSiteHandle() === 'default') {
                $defaultConfig = $config;
            } else {
                $defaultConfig = $siteService->getDefault()->getConfigRepository();
            }

            // Load in default selected plugins and hidden selected plugins
            $selectedPlugins = (array) $defaultConfig->get('editor.ckeditor4.plugins.selected_default', []);
            $selectedPlugins = array_merge($selectedPlugins, (array) $defaultConfig->get('editor.ckeditor4.plugins.selected_hidden', []));
        }

        return $selectedPlugins;
    }
}
