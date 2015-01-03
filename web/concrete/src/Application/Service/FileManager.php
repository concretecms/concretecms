<?php
namespace Concrete\Core\Application\Service;

use View;
use Loader;
use \Concrete\Core\File\Type\Type as FileType;
use File;

class FileManager
{

    /**
     * Sets up a file field for use with a block.
     *
     * @param string $id The ID of your form field
     * @param string $postname The name of your database column into which you'd like to save the file ID
     * @param string $chooseText
     * @param \File $bf
     * @return string $html
     */
    public function file($id, $postname, $chooseText, $bf = null, $filterArgs = array())
    {
        $fileID = 0;
        $v = View::getInstance();
        $v->requireAsset('core/file-manager');

        /**
         * If $_POST[$postname] is a valid File ID
         * use that file
         * If not try to use the $bf parameter passed in
         */
        $vh = Loader::helper('validation/numbers');
        if (isset($_POST[$postname]) && $vh->integer($_POST[$postname])) {
            $postFile = File::getByID($_POST[$postname]);
            if (is_object($postFile) && $postFile->getFileID() > 0) {
                $bf = $postFile;
            }
        }

        if (is_object($bf) && $bf->getFileID() > 0) {
            $fileID = $bf->getFileID();
        }

        $filters = '[]';
        if ( $filterArgs['filters'] ) $filters = json_encode($filterArgs['filters']);

        if ($fileID) {
            $args = "{'inputName': '{$postname}', 'fID': {$fileID}, 'filters': $filters }";
        } else {
            $args = "{'inputName': '{$postname}', 'filters': $filters }";
        }


        $html = <<<EOL
		<div class="ccm-file-selector" data-file-selector="{$id}"></div>
		<script type="text/javascript">
		$(function() {
			$('[data-file-selector={$id}]').concreteFileSelector({$args});
		});
		</script>
EOL;
        /*
         * $html = '<div id="' . $id . '-fm-selected" class="ccm-file-selected-wrapper">'; $html .= '<div class="ccm-file-manager-select" id="' . $id . '-fm-display" ccm-file-manager-field="' . $id . '" style="display: ' . $resetDisplay . '">'; $html .= '<a href="javascript:void(0)" onclick="ccm_chooseAsset=false; ccm_alLaunchSelectorFileManager(\'' . $id . '\')">' . $chooseText . '</a>'; $html .= '</div><input id="' . $id . '-fm-value" type="hidden" name="' . $postname . '" value="' . $fileID . '" />'; $html .= '<script type="text/javascript">$(function() { if (is_object($bf) && (!$bf->isError()) && $bf->getFileID() > 0) { $html .= '<script type="text/javascript">$(function() { ccm_triggerSelectFile(' . $fileID . ', \'' . $id . '\'); });</script>'; }
         */

        return $html;
    }

    /**
     * Sets up an image to be chosen for use with a block.
     *
     * @param string $id The ID of your form field
     * @param string $postname The name of your database column into which you'd like to save the file ID
     * @param string $chooseText
     * @param \File $fileInstanceBlock
     * @param array $additionalArgs
     * @return string $html
     */
    public function image($id, $postname, $chooseText, $fileInstanceBlock = null, $additionalArgs = array())
    {
        $args = array();
        $args['filters'] = array( array( 'field' => 'type', 'type' => FileType::T_IMAGE ) );
        $args = array_merge($args, $additionalArgs);
        return $this->file($id, $postname, $chooseText, $fileInstanceBlock, $args);
    }

    /**
     * Sets up a video to be chosen for use with a block.
     *
     * @param string $id  The ID of your form field
     * @param string $postname The name of your database column into which you'd like to save the file ID
     * @param string $chooseText
     * @param \File $fileInstanceBlock
     * @param array $additionalArgs
     * @return string $html
     */
    public function video($id, $postname, $chooseText, $fileInstanceBlock = null, $additionalArgs = array())
    {
        $args = array();
        $args['filters'] = array( array( 'field' => 'type', 'type' => FileType::T_VIDEO) );
        $args = array_merge($args, $additionalArgs);
        return $this->file($id, $postname, $chooseText, $fileInstanceBlock, $args);
    }

    /**
     * Sets up a text file to be chosen for use with a block.
     *
     * @param string $id The ID of your form field
     * @param string $postname The name of your database column into which you'd like to save the file ID
     * @param string $chooseText
     * @param \File $fileInstanceBlock
     * @param array $additionalArgs
     * @return string $html
     */
    public function text($id, $postname, $chooseText, $fileInstanceBlock = null, $additionalArgs = array())
    {
        $args = array();
        $args['filters'] = array( array( 'field' => 'type', 'type' => FileType::T_TEXT) );
        $args = array_merge($args, $additionalArgs);
        return $this->file($id, $postname, $chooseText, $fileInstanceBlock, $args);
    }

    /**
     * Sets up audio to be chosen for use with a block.
     *
     * @param string $id The ID of your form field
     * @param string $postname The name of your database column into which you'd like to save the file ID
     * @param string $chooseText
     * @param \File $fileInstanceBlock
     * @param array $additionalArgs
     * @return string $html
     */
    public function audio($id, $postname, $chooseText, $fileInstanceBlock = null, $additionalArgs = array())
    {
        $args = array();
        $args['filters'] = array( array( 'field' => 'type', 'type' => FileType::T_AUDIO) );
        $args = array_merge($args, $additionalArgs);
        return $this->file($id, $postname, $chooseText, $fileInstanceBlock, $args);
    }

    /**
     * Sets up a document to be chosen for use with a block.
     *
     * @param string $id  The ID of your form field
     * @param string $postname The name of your database column into which you'd like to save the file ID
     * @param string $chooseText
     * @param \File $fileInstanceBlock
     * @param array $additionalArgs
     * @return string $html
     */
    public function doc($id, $postname, $chooseText, $fileInstanceBlock = null, $additionalArgs = array())
    {
        $args = array();
        $args['filters'] = array( array( 'field' => 'type', 'type' => FileType::T_DOCUMENT) );
        $args = array_merge($args, $additionalArgs);
        return $this->file($id, $postname, $chooseText, $fileInstanceBlock, $args);
    }

    /**
     * Sets up an application to be chosen for use with a block.
     *
     * @param string $id The ID of your form field
     * @param string $postname The name of your database column into which you'd like to save the file ID
     * @param string $chooseText
     * @param \File $fileInstanceBlock
     * @param array $additionalArgs
     * @return string $html
     */
    public function app($id, $postname, $chooseText, $fileInstanceBlock = null, $additionalArgs = array())
    {
        $args = array();
        $args['filters'] = array( array( 'field' => 'type', 'type' => FileType::T_APPLICATION) );
        $args = array_merge($args, $additionalArgs);
        return $this->file($id, $postname, $chooseText, $fileInstanceBlock, $args);
    }
}
