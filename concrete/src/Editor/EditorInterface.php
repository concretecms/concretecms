<?php

namespace Concrete\Core\Editor;

use Concrete\Core\Http\Request;

/**
 * Interface that all rich-text editors must implement.
 */
interface EditorInterface
{
    /**
     * Generate the HTML to be placed in a page to display the inline editor.
     *
     * @param string $key the name of the field to be used to POST the editor content
     * @param string|null $content The initial value of the editor content
     *
     * @return string
     */
    public function outputPageInlineEditor($key, $content = null);

    /**
     * Generate the HTML to be placed in a page to display the editor in composer views.
     *
     * @param string $key the name of the field to be used to POST the editor content
     * @param string $content The initial value of the editor content
     *
     * @return string
     */
    public function outputPageComposerEditor($key, $content);

    /**
     * Generate the HTML to be placed in a page to display the editor when the page is in edito mode.
     *
     * @param string $key the name of the field to be used to POST the editor content
     * @param string $content The initial value of the editor content
     *
     * @return string
     */
    public function outputBlockEditModeEditor($key, $content);

    /**
     * Generate the HTML to be placed in a page to display the editor.
     *
     * @param string $key the name of the field to be used to POST the editor content
     * @param string|null $content The initial value of the editor content
     *
     * @return string
     */
    public function outputStandardEditor($key, $content = null);

    /**
     * Set if the editor can offer the "browse sitemap" feature.
     *
     * @param bool $allow
     */
    public function setAllowSitemap($allow);

    /**
     * Set if the editor can offer the "browse files" feature.
     *
     * @param bool $allow
     */
    public function setAllowFileManager($allow);

    /**
     * Get the plugin manager instance.
     *
     * @return \Concrete\Core\Editor\PluginManager
     */
    public function getPluginManager();

    /**
     * Save the plugin options.
     *
     * @param Request $request
     */
    public function saveOptionsForm(Request $request);

    /**
     * Build the list of required assets.
     */
    public function requireEditorAssets();
}
