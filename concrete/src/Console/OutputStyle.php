<?php

namespace Concrete\Core\Console;

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Terminal;

/**
 * Concrete's output style.
 * Inspiration goes to illuminate/console
 */
class OutputStyle extends SymfonyStyle
{

    /**
     * Ask a question with autocompletion
     *
     * @param $question
     * @param array $choices
     * @param string|null $default
     * @param int|null $attempts
     * @param bool $strict
     * @return string
     */
    public function askWithCompletion($question, array $choices, $default = null, $attempts = null, $strict = false)
    {
        $question = new Question($question, $default);
        $question->setMaxAttempts($attempts)->setAutocompleterValues($choices);

        if ($strict) {
            $question->setValidator(function($result) use ($choices) {
                if (!in_array($result, $choices)) {
                    throw new \InvalidArgumentException(sprintf(
                        'The provided answer is ambiguous. Value should be one of %s',
                        implode(' or ', $choices)));
                }

                return $result;
            });
        }

        return $this->askQuestion($question);
    }

    /**
     * @see askSecretQuestion
     */
    public function secret($question, $fallback = true)
    {
        return $this->askSecretQuestion($question, $fallback);
    }

    /**
     * Ask a question while hiding the response
     *
     * @param $question
     * @param bool $fallback
     * @return string
     */
    public function askSecretQuestion($question, $fallback = true)
    {
        $question = new Question($question);
        $question->setHidden(true)->setHiddenFallback($fallback);
        return $this->askQuestion($question);
    }

    /**
     * Ask a question with a list of allowed answers
     *
     * @param string $question
     * @param array $choices
     * @param string|int|null $default
     * @param int|null $attempts
     * @param bool $multiple
     * @return mixed
     */
    public function choice($question, array $choices, $default = null, $attempts = null, $multiple = false)
    {
        $question = new ChoiceQuestion($question, $choices, $default);
        $question->setMaxAttempts($attempts)->setMultiselect($multiple);
        return $this->askQuestion($question);
    }

    /**
     * Create a table instance
     *
     * @see Table::initStyles()
     * @param array $headers
     * @param array $rows
     * @param string $tableStyle
     * @param array $columnStyles
     */
    public function table(array $headers, array $rows, $tableStyle = 'default', array $columnStyles = [])
    {
        $table = new Table($this);
        $table->setHeaders($headers)->setRows($rows)->setStyle($tableStyle);

        foreach ($columnStyles as $columnIndex => $columnStyle) {
            $table->setColumnStyle($columnIndex, $columnStyle);
        }

        $table->render();
    }

    /**
     * Output in even width columns
     *
     * @param array $values
     * @param int $width
     * @param string $tableStyle
     * @param array $columnStyles
     */
    public function columns(array $values, $width = 5, $tableStyle = 'compact', array $columnStyles = [])
    {
        $table = new Table($this);
        $table->setHeaders([])->setRows(iterator_to_array($this->chunk($values, $width)))->setStyle($tableStyle);

        foreach ($columnStyles as $columnIndex => $columnStyle) {
            $table->setColumnStyle($columnIndex, $columnStyle);
        }

        $columnWidths = $this->getColumnWidths($width);
        $table->setColumnWidths($columnWidths);

        $table->render();
    }

    protected function chunk(array $values, $size)
    {
        $chunk = [];
        foreach ($values as $value) {
            $chunk[] = $value;

            if (count($chunk) === $size) {
                yield $chunk;
                $chunk = [];
            }
        }
        yield $chunk;
    }

    private function getColumnWidths($width)
    {
        $terminal = new Terminal();
        $totalWidth = $terminal->getWidth();
        $left = $width;
        $columns = [];

        while ($left > 1) {
            $columnSize = ceil($totalWidth / $left);
            $columns[] = $columnSize;
            $totalWidth -= $columnSize;
            $left--;
        }

        $columns[] = $totalWidth;

        return $columns;
    }
}
