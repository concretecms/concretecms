<?php

namespace Concrete\Core\Install\Preconditions;

use Concrete\Core\Install\InstallerOptions;
use Concrete\Core\Install\OptionsPreconditionInterface;
use Concrete\Core\Install\PreconditionResult;
use Concrete\Core\Url\UrlImmutable;

class CanonicalUrls implements OptionsPreconditionInterface
{
    /**
     * @var \Concrete\Core\Application\Application
     */
    protected $application;

    /**
     * @var \Concrete\Core\Install\InstallerOptions|null
     */
    protected $installerOptions;

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Install\PreconditionInterface::getName()
     */
    public function getName()
    {
        return t('Canonical URLs');
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Install\PreconditionInterface::getUniqueIdentifier()
     */
    public function getUniqueIdentifier()
    {
        return 'canonical_urls';
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Install\PreconditionInterface::isOptional()
     */
    public function isOptional()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Install\OptionsPreconditionInterface::setInstallerOptions()
     */
    public function setInstallerOptions(InstallerOptions $installerOptions)
    {
        $this->installerOptions = $installerOptions;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Install\PreconditionInterface::performCheck()
     */
    public function performCheck()
    {
        $result = null;
        $configuration = $this->installerOptions->getConfiguration();
        foreach ([
            'canonical-url' => t('The canonical URL must have the http:// scheme or the https:// scheme'),
            'canonical-url-alternative' => t('The alternative canonical URL must have the http:// scheme or the https:// scheme'),
        ] as $handle => $error) {
            if (isset($configuration[$handle]) && $configuration[$handle] !== '') {
                $originalUrl = $configuration[$handle];
                $url = UrlImmutable::createFromUrl($originalUrl);
                if (!preg_match('/^https?$/i', $url->getScheme())) {
                    $result = new PreconditionResult(PreconditionResult::STATE_FAILED, $error);
                    break;
                }
                $finalUrl = (string) $url;
                if ($originalUrl !== $finalUrl) {
                    $result = new PreconditionResult(PreconditionResult::STATE_FAILED, t('The canonical url "%1$s" should be specified as "%2$s"', $originalUrl, $finalUrl));
                }
            }
        }
        if ($result === null) {
            $result = new PreconditionResult(PreconditionResult::STATE_PASSED);
        }

        return $result;
    }
}
