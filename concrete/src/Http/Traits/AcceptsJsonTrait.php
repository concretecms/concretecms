<?php

declare(strict_types=1);

namespace Concrete\Core\Http\Traits;

use Symfony\Component\HttpFoundation\Request;

defined('C5_EXECUTE') or die('Access Denied.');

trait AcceptsJsonTrait
{
    /**
     * Does a request expect a JSON response?
     */
    protected static function requestExpectsJson(Request $request): bool
    {
        if (in_array($request->getPreferredFormat(), ['json', 'jsonld'], true)) {
            return true;
        }
        // a media type may tell JSON is the syntax it's written in, as application/problem+json does
        $acceptable = $request->getAcceptableContentTypes();

        return substr(strtolower($acceptable[0] ?? ''), -5) === '+json';
    }

    /**
     * Can a request be answered with JSON?
     *
     * Besides the ones expecting it, that's the case for the calls made with XMLHttpRequest:
     * they may expect something else when all goes well, but they can always read the errors
     * we hand them, which a page or a redirect wouldn't let them.
     */
    protected static function requestAcceptsJson(Request $request): bool
    {
        return $request->isXmlHttpRequest() || static::requestExpectsJson($request);
    }
}
