<?php

namespace Concrete\Core\Backup\ContentImporter\ValueInspector\InspectionRoutine;

abstract class AbstractRegularExpressionRoutine implements RoutineInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Backup\ContentImporter\ValueInspector\InspectionRoutine\RoutineInterface::match()
     */
    public function match($content)
    {
        $items = [];
        if (is_scalar($content)) {
            $matches = null;
            if (preg_match_all($this->getRegularExpression(), (string) $content, $matches)) {
                if (isset($matches[1])) {
                    foreach ($matches[1] as $identifier) {
                        $items[] = $this->getItem($identifier);
                    }
                }
            }
        }

        return $items;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Backup\ContentImporter\ValueInspector\InspectionRoutine\RoutineInterface::replaceContent()
     */
    public function replaceContent($content)
    {
        if (is_scalar($content)) {
            $content = preg_replace_callback(
                $this->getRegularExpression(),
                function (array $matches) {
                    if (isset($matches[1])) {
                        return $this->getItem($matches[1])->getContentValue();
                    }
                },
                (string) $content
            );
        }

        return $content;
    }

    /**
     * Get the regular expression to be used to extract the identifier.
     * The identifier must be the first capture group.
     *
     * @return string
     */
    abstract public function getRegularExpression();

    /**
     * Resolves an item given its identifier.
     *
     * @param string $identifier
     *
     * @return \Concrete\Core\Backup\ContentImporter\ValueInspector\Item\ItemInterface
     */
    abstract public function getItem($identifier);
}
