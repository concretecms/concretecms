<?php
namespace Concrete\Core\Summary\Data\Field;

use Concrete\Core\User\UserInfo;
use Symfony\Component\Serializer\Normalizer\DenormalizableInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Concrete\Core\File\File;

class AuthorDataFieldData implements DataFieldDataInterface
{

    /**
     * @var array
     */
    protected $data;

    public function getName()
    {
        return $this->data['name'];
    }

    public function getUserID()
    {
        return $this->data['id'];
    }

    public function getAvatar()
    {
        return $this->data['avatar'];
    }

    public function __construct(UserInfo $ui = null)
    {
        if ($ui) {
            $data = [
                'name' => $ui->getUserDisplayName(),
                'id' => $ui->getUserID(),
                'avatar' => $ui->getUserAvatar()->getPath(),
            ];
            $this->data = $data;
        }
    }

    public function setData(array $data): void
    {
        $this->data = $data;
    }

    public function __toString()
    {
        return $this->data['name'];
    }

    public function jsonSerialize()
    {
        return [
            'class' => self::class,
            'author' => $this->data,
        ];
    }

    public function denormalize(DenormalizerInterface $denormalizer, $data, $format = null, array $context = [])
    {
        if (isset($data['author'])) {
            $this->setData($data['author']);
        }
    }
}
