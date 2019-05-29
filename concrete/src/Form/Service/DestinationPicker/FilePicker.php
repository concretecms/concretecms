<?php

namespace Concrete\Core\Form\Service\DestinationPicker;

use ArrayAccess;
use Concrete\Core\Application\Service\FileManager;
use Concrete\Core\Entity\File\File;
use Concrete\Core\Utility\Service\Validation\Numbers;
use Doctrine\ORM\EntityManagerInterface;

/**
 * A picker for DestinationPicker that allows users specify a concrete5 file.
 *
 * Supported options for the generate method:
 * - displayName: the display name of this picker (to be used in the SELECT html element).
 * - chooseFileText: the text to be used for the "Choose File" button
 * - any other option will be passed to the concrete5 File Manager picker (for example: 'filters' may be useful - @see \Concrete\Core\Application\Service\FileManager::file() )
 *
 * Supported options for the decode method:
 * - none
 */
class FilePicker implements PickerInterface
{
    /**
     * @var \Concrete\Core\Application\Service\FileManager
     */
    protected $fileManager;

    /**
     * @var \Concrete\Core\Utility\Service\Validation\Numbers
     */
    protected $numbers;

    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    protected $entityManager;

    public function __construct(FileManager $fileManager, Numbers $numbers, EntityManagerInterface $entityManager)
    {
        $this->fileManager = $fileManager;
        $this->numbers = $numbers;
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Form\Service\DestinationPicker\PickerInterface::getDisplayName()
     */
    public function getDisplayName(array $options)
    {
        return empty($options['displayName']) ? t('File') : $options['displayName'];
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Form\Service\DestinationPicker\PickerInterface::getHeight()
     */
    public function getHeight()
    {
        return 60;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Form\Service\DestinationPicker\PickerInterface::generate()
     */
    public function generate($pickerKey, array $options, $selectedValue = null)
    {
        if (is_object($selectedValue)) {
            $fileID = (int) $selectedValue->getFileID();
        } elseif (empty($selectedValue)) {
            $fileID = 0;
        } else {
            $fileID = (int) $selectedValue;
        }
        $args = $options;
        unset($args['displayName']);
        unset($args['chooseFileText']);

        return $this->fileManager->file($pickerKey, $pickerKey, empty($options['chooseFileText']) ? t('Choose File') : $options['chooseFileText'], $fileID, $args);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Form\Service\DestinationPicker\PickerInterface::decode()
     */
    public function decode(array $data, $pickerKey, array $options, ArrayAccess $errors = null, $fieldDisplayName = null)
    {
        $result = null;
        $postValue = array_get($data, $pickerKey);
        if ($this->numbers->integer($postValue, 1)) {
            $postValue = (int) $postValue;
            if ($this->entityManager->find(File::class, $postValue) === null) {
                if ($errors !== null) {
                    if ((string) $fieldDisplayName === '') {
                        $errors[] = t('Unable to find the specified file.');
                    } else {
                        $errors[] = t('Unable to find the file specified for %s.', $fieldDisplayName);
                    }
                }
            } else {
                $result = $postValue;
            }
        }

        return $result;
    }
}
