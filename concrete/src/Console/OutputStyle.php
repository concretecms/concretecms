<?php

namespace Concrete\Core\Console;

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * concrete5's output style.
 * Inspiration goes to illuminate/console
 */
class OutputStyle extends SymfonyStyle
{

    /** @var \Symfony\Component\Console\Output\OutputInterface */
    private $output;

    /**
     * Create a new Console OutputStyle instance.
     *
     * @param  \Symfony\Component\Console\Input\InputInterface  $input
     * @param  \Symfony\Component\Console\Output\OutputInterface  $output
     * @return void
     */
    public function __construct(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;
        parent::__construct($input, $output);
    }

    /**
     * Ask a question with autocompletion
     *
     * @param $question
     * @param array $choices
     * @param null $default
     * @return string
     */
    public function askWithCompletion($question, array $choices, $default = null)
    {
        $question = new Question($question, $default);
        $question->setAutocompleterValues($choices);
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
     * @param null $default
     * @param null $attempts
     * @param null $multiple
     * @return mixed
     */
    public function choice($question, array $choices, $default = null, $attempts = null, $multiple = null)
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
}
