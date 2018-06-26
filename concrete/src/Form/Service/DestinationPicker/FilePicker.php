<?php

namespace Concrete\Core\Form\Service\DestinationPicker;

use ArrayAccess;
use Concrete\Core\Application\Service\FileManager;
use Concrete\Core\Entity\File\File;
use Concrete\Core\Utility\Service\Validation\Numbers;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\ParameterBag;

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
     * @see \Concrete\Core\Form\Service\DestinationPicker\PickerInterface::getPostName()
     */
    public function getPostName($key, array $options)
    {
        return empty($options['postName']) ? "{$key}_fid" : $options['postName'];
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Form\Service\DestinationPicker\PickerInterface::generate()
     */
    public function generate($key, array $options, $selectedValue = null)
    {
        $postName = $this->getPostName($key, $options);
        if (is_object($selectedValue)) {
            $fileID = (int) $selectedValue->getFileID();
        } elseif (empty($selectedValue)) {
            $fileID = 0;
        } else {
            $fileID = (int) $selectedValue;
        }
        $args = $options;
        unset($args['displayName']);
        unset($args['postName']);
        unset($args['chooseFileText']);

        return $this->fileManager->file($postName, $postName, empty($options['chooseFileText']) ? t('Choose File') : $options['chooseFileText'], $fileID, $args);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Form\Service\DestinationPicker\PickerInterface::decode()
     */
    public function decode(ParameterBag $data, $key, array $options, ArrayAccess $errors = null)
    {
        $result = null;
        $postName = $this->getPostName($key, $options);
        $postValue = $data->get($postName);
        if ($this->numbers->integer($postValue, 1)) {
            $postValue = (int) $postValue;
            if ($this->entityManager->find(File::class, $postValue) === null) {
                if ($errors !== null) {
                    $errors[] = t('Unable to find the specified file.');
                }
            } else {
                $result = $postValue;
            }
        }

        return $result;
    }
}
