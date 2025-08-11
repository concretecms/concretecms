<?php
namespace Concrete\Core\Api\Fractal\Transformer\Traits;

trait SanitizableContentTrait
{

    public function stripAllTags(string $content): string
    {
        // Remove carriage returns from content
        $content = str_replace(["\r\n", "\r"], "\n", $content);

        $dom = new \DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML($content, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();
        libxml_use_internal_errors(false);

        $removeTags = ['script', 'style'];
        foreach ($removeTags as $tagString) {
            $stripped[$tagString] = 0;

            // Repeatedly search and remove found tags until they are gone. I'm not sure exactly why this is necessary,
            // perhaps removing the first element detaches the rest from the new dom and so remove doesn't mutate the
            // new state?
            $maxIterations = 1000;
            do {
                $elements = $dom->getElementsByTagName($tagString);
                foreach ($elements as $tag) {
                    $stripped[$tagString]++;
                    $tag->parentNode->removeChild($tag);
                }
            } while($elements->count() && $maxIterations--);
        }
        return $dom->saveXML($dom->documentElement);
    }
}
