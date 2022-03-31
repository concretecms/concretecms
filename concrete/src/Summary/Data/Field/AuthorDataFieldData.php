<?php
namespace Concrete\Core\Summary\Data\Field;

use Concrete\Core\User\UserInfo;
use Concrete\Core\User\UserInfoRepository;
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
        // This used to be cached with the board but we should probably make it dynamic anyway
        // so that updated images happen when users change their avatar. Also fixes #9822
        if (!isset($this->data['avatar'])) {
            $ui = app(UserInfoRepository::class)->getByID($this->data['id']);
            if ($ui) {
                $this->data['avatar'] = $ui->getUserAvatar()->getPath();
            }
        }
        return $this->data['avatar'];
    }

    public function __construct(UserInfo $ui = null)
    {
        if ($ui) {
            $data = [
                'name' => $ui->getUserDisplayName(),
                'id' => $ui->getUserID(),
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

    #[\ReturnTypeWillChange]
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
