<?php
namespace Concrete\Core\Service\Detector;

interface DetectorInterface
{
    /**
     * Determine whether this environment matches the expected service environment.
     * Returns null if not matched, or the service version if it's matched (empty string if the version is not detected).
     *
     * @return null|string
     */
    public function detect();
}
