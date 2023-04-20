<?php
namespace Concrete\Core\Install\StartingPoint\Installer\Routine;

use Concrete\Core\Backup\ContentImporter;
use Illuminate\Filesystem\Filesystem;

class InstallFeatureContentRoutine extends AbstractRoutine
{

    /**
     * @var string
     */
    protected $text;

    /**
     * @var string
     */
    protected $domain;

    /**
     * @var string
     */
    protected $feature;

    public function __construct(string $feature, string $domain, string $text = null)
    {
        $this->text = $text;
        $this->domain = $domain;
        $this->feature = $feature;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @return string
     */
    public function getDomain(): string
    {
        return $this->domain;
    }

    /**
     * @return string
     */
    public function getFeature(): string
    {
        return $this->feature;
    }

    public function getContentFile(): string
    {
        return sprintf(DIR_BASE_CORE . '/features/%s/%s.xml', $this->getDomain(), $this->getFeature());
    }

    public function hasContentFile(): bool
    {
        $filesystem = new Filesystem();
        $file = $this->getContentFile();
        return $filesystem->exists($file);
    }

}
