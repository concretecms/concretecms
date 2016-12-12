<?php
namespace Concrete\Core\Backup\ContentImporter\ValueInspector\InspectionRoutine;

abstract class AbstractRegularExpressionRoutine implements RoutineInterface
{
    abstract public function getRegularExpression();
    abstract public function getItem($identifier);

    public function match($content)
    {
        $items = array();
        if (is_scalar($content)) {
            if (preg_match_all($this->getRegularExpression(), $content, $matches)) {
                if ($matches[1]) {
                    foreach ($matches[1] as $identifier) {
                        $items[] = $this->getItem($identifier);
                    }
                }
            }
        }

        return $items;
    }

    public function replaceContent($content)
    {
        $routine = $this;
        if (is_scalar($content)) {
            $content = preg_replace_callback(
                $this->getRegularExpression(),
                function ($matches) use ($routine) {
                    if (isset($matches[1])) {
                        $identifier = $matches[1];
                        $item = $routine->getItem($identifier);

                        return $item->getContentValue();
                    }
                },
                $content
            );
        }

        return $content;
    }
}
