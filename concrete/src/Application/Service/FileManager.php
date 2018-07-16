<?php

namespace Concrete\Core\Application\Service;

use Concrete\Core\Entity\File\File as FileEntity;
use Concrete\Core\File\Type\Type as FileType;
use Concrete\Core\Http\Request;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\View\View;
use Doctrine\ORM\EntityManagerInterface;

class FileManager
{
    /**
     * Sets up a form field to let users pick a file.
     *
     * @param string $inputID The ID of the form field
     * @param string $inputName The name of the form field (the selected file ID will be posted with this name)
     * @param string $chooseText The text to be used to tell users "Choose a File"
     * @param \Concrete\Core\Entity\File\File|\Concrete\Core\Entity\File\Version|int|null $preselectedFile the pre-selected file (or its ID)
     * @param array $args An array with additional arguments. Supported array keys are:
     * <ul>
     *     <li><code>'filters'</code><br />
     *         A list of file filters. Every array item must have a 'field' key (with the name of the field as the value), and another field that's specific of the field.<br />
     *         For a list of valid field identifiers, see the definition of the loadDataFromRequest method of the classes that implement \ Concrete\Core\Search\Field\FieldInterface (usually it's the requestVariables property).
     *     </li>
     * </ul>A list of arrays. Each array item must have a 'field' key whose value is the name of the field, and
     *
     * @return string $html
     *
     * @example <code><pre>
     * $app = \Concrete\Core\Support\Facade\Application::getFacadeApplication();
     * $filemanager = $app->make(\Concrete\Core\Application\Service\FileManager::class);
     * echo $filemanager->file(
     *     'myId',
     *     'myName',
     *     t('Choose File'),
     *     $preselectedFile,
     *     [
     *         'filters' => [
     *             [
     *                 'field' => 'type',
     *                 'type' => \Concrete\Core\File\Type\Type::T_IMAGE,
     *             ],
     *             [
     *                 'field' => 'extension',
     *                 'extension' => ['.png', '.jpg'],
     *             ],
     *         ],
     *     ]
     * )
     * </pre></code>
     */
    public function file($inputID, $inputName, $chooseText, $preselectedFile = null, $args = [])
    {
        $app = Application::getFacadeApplication();

        $view = View::getInstance();
        $request = $app->make(Request::class);
        $vh = $app->make('helper/validation/numbers');

        $view->requireAsset('core/file-manager');
        $fileSelectorArguments = [
            'inputName' => (string) $inputName,
            'fID' => null,
            'filters' => [],
        ];
        if ($vh->integer($request->request->get($inputName))) {
            $file = $app->make(EntityManagerInterface::class)->find(FileEntity::class, $request->request->get($inputName));
            if ($file !== null) {
                $fileSelectorArguments['fID'] = $file->getFileID();
            }
        } elseif ($vh->integer($preselectedFile)) {
            $fileSelectorArguments['fID'] = (int) $preselectedFile;
        } elseif (is_object($preselectedFile)) {
            $fileSelectorArguments['fID'] = (int) $preselectedFile->getFileID();
        }
        if ($fileSelectorArguments['fID'] === null && (string) $chooseText !== '') {
            $fileSelectorArguments['chooseText'] = (string) $chooseText;
        }

        if (isset($args['filters']) && is_array($args['filters'])) {
            $fileSelectorArguments['filters'] = $args['filters'];
        }

        $fileSelectorArgumentsJson = json_encode($fileSelectorArguments);
        $html = <<<EOL
<div class="ccm-file-selector" data-file-selector="{$inputID}"></div>
<script type="text/javascript">
$(function() {
    $('[data-file-selector="{$inputID}"]').concreteFileSelector({$fileSelectorArgumentsJson});
});
</script>
EOL;

        return $html;
    }

    /**
     * Sets up a form field to let users pick an image file.
     *
     * @param string $inputID The ID of the form field
     * @param string $inputName The name of the form field (the selected file ID will be posted with this name)
     * @param string $chooseText The text to be used to tell users "Choose a File"
     * @param \Concrete\Core\Entity\File\File|\Concrete\Core\Entity\File\Version|int|null $preselectedFile the pre-selected file (or its ID)
     * @param array $args See the $args description of the <code>file</code> method
     *
     * @return string $html
     *
     * @see \Concrete\Core\Application\Service\FileManager::file()
     */
    public function image($inputID, $inputName, $chooseText, $preselectedFile = null, $args = [])
    {
        return $this->fileOfType(FileType::T_IMAGE, $inputID, $inputName, $chooseText, $preselectedFile, $args);
    }

    /**
     * Sets up a form field to let users pick a video file.
     *
     * @param string $inputID The ID of the form field
     * @param string $inputName The name of the form field (the selected file ID will be posted with this name)
     * @param string $chooseText The text to be used to tell users "Choose a File"
     * @param \Concrete\Core\Entity\File\File|\Concrete\Core\Entity\File\Version|int|null $preselectedFile the pre-selected file (or its ID)
     * @param array $args See the $args description of the <code>file</code> method
     *
     * @return string $html
     *
     * @see \Concrete\Core\Application\Service\FileManager::file()
     */
    public function video($inputID, $inputName, $chooseText, $preselectedFile = null, $args = [])
    {
        return $this->fileOfType(FileType::T_VIDEO, $inputID, $inputName, $chooseText, $preselectedFile, $args);
    }

    /**
     * Sets up a form field to let users pick a text file.
     *
     * @param string $inputID The ID of your form field
     * @param string $inputName The name of the form field (the selected file ID will be posted with this name)
     * @param string $chooseText The text to be used to tell users "Choose a File"
     * @param \Concrete\Core\Entity\File\File|\Concrete\Core\Entity\File\Version|int|null $preselectedFile the pre-selected file (or its ID)
     * @param array $args See the $args description of the <code>file</code> method
     *
     * @return string $html
     *
     * @see \Concrete\Core\Application\Service\FileManager::file()
     */
    public function text($inputID, $inputName, $chooseText, $preselectedFile = null, $args = [])
    {
        return $this->fileOfType(FileType::T_TEXT, $inputID, $inputName, $chooseText, $preselectedFile, $args);
    }

    /**
     * Sets up a form field to let users pick an audio file.
     *
     * @param string $inputID The ID of your form field
     * @param string $inputName The name of the form field (the selected file ID will be posted with this name)
     * @param string $chooseText The text to be used to tell users "Choose a File"
     * @param \Concrete\Core\Entity\File\File|\Concrete\Core\Entity\File\Version|int|null $preselectedFile the pre-selected file (or its ID)
     * @param array $args See the $args description of the <code>file</code> method
     *
     * @return string $html
     *
     * @see \Concrete\Core\Application\Service\FileManager::file()
     */
    public function audio($inputID, $inputName, $chooseText, $preselectedFile = null, $args = [])
    {
        return $this->fileOfType(FileType::T_AUDIO, $inputID, $inputName, $chooseText, $preselectedFile, $args);
    }

    /**
     * Sets up a form field to let users pick a document file.
     *
     * @param string $inputID  The ID of your form field
     * @param string $inputName The name of the form field (the selected file ID will be posted with this name)
     * @param string $chooseText The text to be used to tell users "Choose a File"
     * @param \Concrete\Core\Entity\File\File|\Concrete\Core\Entity\File\Version|int|null $preselectedFile the pre-selected file (or its ID)
     * @param array $args See the $args description of the <code>file</code> method
     *
     * @return string $html
     *
     * @see \Concrete\Core\Application\Service\FileManager::file()
     */
    public function doc($inputID, $inputName, $chooseText, $preselectedFile = null, $args = [])
    {
        return $this->fileOfType(FileType::T_DOCUMENT, $inputID, $inputName, $chooseText, $preselectedFile, $args);
    }

    /**
     * Sets up a form field to let users pick a application file.
     *
     * @param string $inputID The ID of your form field
     * @param string $inputName The name of the form field (the selected file ID will be posted with this name)
     * @param string $chooseText The text to be used to tell users "Choose a File"
     * @param \Concrete\Core\Entity\File\File|\Concrete\Core\Entity\File\Version|int|null $preselectedFile the pre-selected file (or its ID)
     * @param array $args See the $args description of the <code>file</code> method
     *
     * @return string $html
     *
     * @see \Concrete\Core\Application\Service\FileManager::file()
     */
    public function app($inputID, $inputName, $chooseText, $preselectedFile = null, $args = [])
    {
        return $this->fileOfType(FileType::T_APPLICATION, $inputID, $inputName, $chooseText, $preselectedFile, $args);
    }

    /**
     * Sets up a form field to let users pick a file of a specific type.
     *
     * @param int $type One of the \Concrete\Core\File\Type\Type::T_... constants.
     * @param string $inputID The ID of your form field
     * @param string $inputName The name of the form field (the selected file ID will be posted with this name)
     * @param string $chooseText The text to be used to tell users "Choose a File"
     * @param \Concrete\Core\Entity\File\File|\Concrete\Core\Entity\File\Version|int|null $preselectedFile the pre-selected file (or its ID)
     * @param array $args See the $args description of the <code>file</code> method
     *
     * @return string
     *
     * @see \Concrete\Core\Application\Service\FileManager::file()
     */
    private function fileOfType($type, $inputID, $inputName, $chooseText, $preselectedFile = null, $args = [])
    {
        if (!is_array($args)) {
            $args = [];
        }
        if (!isset($args['filters']) || !is_array($args['filters'])) {
            $args['filters'] = [];
        }
        $args['filters'] = array_merge(
            [
                [
                    'field' => 'type',
                    'type' => (int) $type,
                ],
            ],
            $args['filters']
        );

        return $this->file($inputID, $inputName, $chooseText, $preselectedFile, $args);
    }
}
