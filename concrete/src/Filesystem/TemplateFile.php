<?php
namespace Concrete\Core\Filesystem;

use Loader;

class TemplateFile
{
    /** Stores the parent object of this template file
     * @var \Concrete\Core\Entity\Block\BlockType\BlockType
     */
    protected $parentObject;
    /** Stores the file name
     * @var string
     */
    protected $filename;
    /** Stores the name of this template file
     * @var string
     */
    protected $name;
    /** Initializes this TemplateFile instance
     * @param \Concrete\Core\Entity\Block\BlockType\BlockType $parentObject The parent object of this template file
     * @param string $filename The file name
     */
    public function __construct($parentObject, $filename)
    {
        $this->parentObject = $parentObject;
        $this->filename = $filename;
        $baseName = $filename;
        if (strpos($baseName, '.') !== false) {
            // This used to be strrpos - which makes sense to get rid of the extension, but we can't do that
            // because now our templates could be either ".php" (one period) or ".html.twig" (two periods)
            $baseName = substr($baseName, 0, strpos($baseName, '.'));
        }
        $this->name = Loader::helper('text')->unhandle($baseName);
    }
    /** Returns the parent object of this template file
     * @return \Concrete\Core\Entity\Block\BlockType\BlockType
     */
    public function getTemplateFileParentObject()
    {
        return $this->parentObject;
    }
    /** Returns the file name
     * @return string
     */
    public function getTemplateFileFilename()
    {
        return $this->filename;
    }
    /** Returns the name of this template file
     * @return string
     */
    public function getTemplateFileName()
    {
        return $this->name;
    }
    /** Returns the display name for this template file (localized and escaped accordingly to $format)
     * @param string $format = 'html' Escape the result in html format (if $format is 'html'). If $format is 'text' or any other value, the display name won't be escaped.
     *
     * @return string
     */
    public function getTemplateFileDisplayName($format = 'html')
    {
        $displayName = tc('TemplateFileName', $this->name);
        switch ($format) {
            case 'html':
                return h($displayName);
            case 'text':
            default:
                return $displayName;
        }
    }
    /** Returns the file name (implemented for backward compatibility with previuos BlockType->getBlockTypeCustomTemplates / BlockType->getBlockTypeComposerTemplates
     * @return string
     */
    public function __toString()
    {
        return $this->filename;
    }
    /** Sorts a list of TemplateFile instances
     * @param TemplateFile[] $list The list of TemplateFile instances to be sorted
     *
     * @return TemplateFile[]
     */
    public static function sortTemplateFileList($list)
    {
        usort($list, '\Concrete\Core\Filesystem\TemplateFile::sortTemplateFileListSorter');

        return $list;
    }
    /** Callable function used by sortTemplateFileList.
     * @param TemplateFile $a
     * @param TemplateFile $b
     *
     * @return int
     */
    protected static function sortTemplateFileListSorter($a, $b)
    {
        return strcasecmp($a->getTemplateFileDisplayName('text'), $b->getTemplateFileDisplayName('text'));
    }
}
