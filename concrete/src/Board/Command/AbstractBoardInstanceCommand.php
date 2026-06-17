<?php

namespace Concrete\Core\Board\Command;

use Concrete\Core\Entity\Board\Instance;
use Concrete\Core\Foundation\Command\Command;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Serializer\Normalizer\DenormalizableInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizableInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

abstract class AbstractBoardInstanceCommand extends Command implements NormalizableInterface, DenormalizableInterface
{
    use BoardInstanceTrait;

    public function normalize(NormalizerInterface $normalizer, ?string $format = null, array $context = [])
    {
        return [
            'boardInstanceID' => $this->getInstance()->getBoardInstanceID(),
        ];
    }

    public function denormalize(DenormalizerInterface $denormalizer, $data, ?string $format = null, array $context = [])
    {
        if (isset($data['boardInstanceID'])) {
            $instance = app(EntityManager::class)->find(Instance::class, $data['boardInstanceID']);
            if ($instance) {
                $this->setInstance($instance);
            }
        }
    }

}
