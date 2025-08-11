<?php

namespace Concrete\Core\SiteInformation;

use Concrete\Core\SiteInformation\Question\QuestionInterface;

abstract class AbstractSurvey implements SurveyInterface
{

    /**
     * @return QuestionInterface[]
     */
    abstract public function getQuestions(): array;

    abstract public function getSaverNamespace(): string;

    public function getResult($question)
    {
        /**
         * @var $question QuestionInterface|string
         */
        $saver = $this->getSaver();
        $results = $saver->getResults();
        $key = $question::getKey();
        return $results[$key] ?? null;
    }

    public function render(): string
    {
        $results = $this->getSaver()->getResults();
        $output = '';
        foreach ($this->getQuestions() as $question) {
            $output .= $question->getTag($results)->render();
        }
        return $output;
    }

    public function getSaver(): SaverInterface
    {
        $fieldKeys = [];
        foreach ($this->getQuestions() as $question) {
            $fieldKeys[] = $question->getKey();
        }
        return app(DatabaseConfigSaver::class, [
            'fieldKeys' => $fieldKeys,
            'namespace' => $this->getSaverNamespace()
        ]);
    }

}
