<?php
namespace Concrete\Core\SiteInformation;

use Symfony\Component\HttpFoundation\Request;

interface SaverInterface
{

    public function saveFromRequest(Request $request);

    /**
     * Returns an array with keys/values for the saved results.
     *
     * @return array
     */
    public function getResults(): array;
}
