<?php

namespace Concrete\Core\Serializer\Normalizer;

use Concrete\Core\Entity\File\File;
use Concrete\Core\File\File as FileService;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class FileNormalizer implements NormalizerInterface, DenormalizerInterface
{
    /**
     * @param array<array-key, int> $data
     * @param string $type
     * @param string|null $format
     * @param mixed[] $context
     *
     * @return File|null
     */
    public function denormalize($data, string $type, ?string $format = null, array $context = []): ?File
    {
        if ($data) {
            return FileService::getByID($data['id']);
        }

        return null;
    }

    /**
     * @param $data
     * @param string $type
     * @param string|null $format
     * @param mixed[] $context
     *
     * @return bool
     */
    public function supportsDenormalization($data, string $type, ?string $format = null, array $context = [])
    {
        return $type === File::class;
    }

    /**
     * @param $data
     * @param string|null $format
     * @param mixed[] $context
     *
     * @return bool
     */
    public function supportsNormalization($data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof File;
    }

    /**
     * @param File $object
     * @param string|null $format
     * @param mixed[] $context
     *
     * @return array<array-key, string|int|null>
     */
    public function normalize($object, ?string $format = null, array $context = []): array
    {
        $version = $object->getApprovedVersion();

        return [
            'id' => $object->getFileID(),
            'downloadUrl' => $version ? (string) $version->getDownloadURL() : null,
            'relativePath' => $version?->getRelativePath(),
            'fileName' => $version?->getFileName(),
            'description' => $version?->getDescription(),
            'title' => $version?->getTitle(),
        ];
    }
}
