<?php

namespace Concrete\Core\Providers;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Foundation\Service\Provider;
use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\YamlEncoder;
use Symfony\Component\Serializer\Mapping\ClassDiscriminatorFromClassMetadata;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\NameConverter\MetadataAwareNameConverter;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\BackedEnumNormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\JsonSerializableNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use Concrete\Core\Serializer\Normalizer\FileNormalizer;
use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;
use Symfony\Component\PropertyAccess\PropertyAccessor;

class SerializerServiceProvider extends Provider
{
    public function register(): void
    {
        $this->app->singleton(SerializerInterface::class, function () {
            return $this->createSerializer();
        });

        $this->app->singleton(DenormalizerInterface::class, function () {
            return $this->createSerializer();
        });

        $this->app->singleton(NormalizerInterface::class, function () {
            return $this->createSerializer();
        });

        $this->app->singleton(Serializer::class, function () {
            return $this->createSerializer();
        });
    }

    private function createSerializer(): Serializer
    {
        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $nameConverter = new MetadataAwareNameConverter($classMetadataFactory);

        $propertyAccessor = new PropertyAccessor();

        $phpDocExtractor = new PhpDocExtractor();
        $reflectionExtractor = new ReflectionExtractor();
        $propertyTypeExtractor = new PropertyInfoExtractor(
            [$reflectionExtractor],
            [$phpDocExtractor, $reflectionExtractor]
        );
        $classDiscriminatorResolver = new ClassDiscriminatorFromClassMetadata($classMetadataFactory);

        $objectNormalizer = new ObjectNormalizer(
            $classMetadataFactory,
            $nameConverter,
            $propertyAccessor,
            $propertyTypeExtractor,
            $classDiscriminatorResolver
        );
        $encoders = [
            new CsvEncoder(),
            new JsonEncoder(),
            new XmlEncoder(),
            new YamlEncoder(),
        ];

        /**
         * @var Repository $config
         */
        $config = $this->app->make(Repository::class);
        /**
         * @var array<string> $configNormalizers
         */
        $configNormalizers = $config->get('app.serializer.normalizers', []);
        /**
         * @var array<DenormalizerInterface|NormalizerInterface> $appNormalizers
         */
        $appNormalizers = [];
        foreach ($configNormalizers as $normalizer) {
            $appNormalizers[] = $this->app->make($normalizer);
        }

        /**
         * @var array<DenormalizerInterface|NormalizerInterface> $defaultNormalizers
         */
        $defaultNormalizers = [
            $this->app->make(FileNormalizer::class),
            new BackedEnumNormalizer(),
            new DateTimeNormalizer(),
            new JsonSerializableNormalizer(),
            new ArrayDenormalizer(),
            $objectNormalizer,
        ];

        $normalizers = array_merge($appNormalizers, $defaultNormalizers);
        return new Serializer($normalizers, $encoders);
    }
}
