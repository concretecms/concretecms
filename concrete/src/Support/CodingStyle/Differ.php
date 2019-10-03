<?php

namespace Concrete\Core\Support\CodingStyle;

use PhpCsFixer\Differ\DifferInterface;
use SebastianBergmann\Diff\Differ as SBDiffer;

class Differ implements DifferInterface
{
    /**
     * @var int
     */
    protected $maxTrailingUnchangedLines;

    /**
     * @var \SebastianBergmann\Diff\Differ
     */
    protected $differ;

    /**
     * Initialize the instance.
     *
     * @param int $maxTrailingUnchangedLines the maximum number of unchanged trailing lines to be kept
     */
    public function __construct($maxTrailingUnchangedLines = 3)
    {
        $this->maxTrailingUnchangedLines = max(0, (int) $maxTrailingUnchangedLines);
        $this->differ = new SBDiffer();
    }

    /**
     * {@inheritdoc}
     *
     * @see \PhpCsFixer\Differ\DifferInterface::diff()
     */
    public function diff($old, $new)
    {
        $diff = $this->differ->diff($old, $new);
        $lines = explode("\n", rtrim($diff, "\n"));
        $trailingUnchangedLines = $this->countTrailingUnchangedLines($lines);
        $trailingLinesToRemove = $trailingUnchangedLines - $this->maxTrailingUnchangedLines;
        if ($trailingLinesToRemove <= 0) {
            return $diff;
        }
        array_splice($lines, count($lines) - $trailingLinesToRemove);

        return implode("\n", $lines) . "\n";
    }

    /**
     * Count the number of unchanged trailing lines.
     *
     * @param string[] $lines
     *
     * @return string
     */
    protected function countTrailingUnchangedLines(array &$lines)
    {
        $count = 0;
        for ($index = count($lines) - 1; $index >= 0; --$index) {
            if ($lines[$index] === '' || $lines[$index][0] !== ' ') {
                break;
            }
            ++$count;
        }

        return $count;
    }
}
