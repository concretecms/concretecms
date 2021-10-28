<?php

namespace Concrete\Controller\SinglePage\Dashboard\System\Environment;

use Concrete\Core\Database\CharacterSetCollation\Exception;
use Concrete\Core\Database\CharacterSetCollation\Manager;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Page\Controller\DashboardPageController;

class DatabaseCharset extends DashboardPageController
{
    public function view()
    {
        $connection = $this->app->make(Connection::class);
        $this->set('charsetsAndCollations', $this->listCharsetsAndCollations($connection));
        $this->set('collation', $this->getConfiguredCollation($connection));
    }

    public function set_connection_collation()
    {
        if (!$this->token->validate(__FUNCTION__)) {
            $this->error->add($this->token->getErrorMessage());
        } else {
            $collation = $this->request->request->get('collation');
            $manager = $this->app->make(Manager::class);
            $warnings = $this->app->make('error');
            try {
                $manager->apply('', $collation, '', '', null, $warnings);
            } catch (Exception $x) {
                $this->error->add($x);
            }
        }

        if ($this->error->has()) {
            return $this->view();
        }

        if ($warnings->has()) {
            $this->flash('set_connection_collation_warnings', $warnings);
        } else {
            $this->flash('success', t('The character set and the collation of the connection and all the tables have been updated.'));
        }

        return $this->buildRedirect($this->action());
    }

    /**
     * @param \Concrete\Core\Database\Connection\Connection $connection
     *
     * @return array
     */
    protected function listCharsetsAndCollations(Connection $connection): array
    {
        $charsetsAndDefaultCollation = $connection->getSupportedCharsets();
        ksort($charsetsAndDefaultCollation, SORT_NATURAL);
        $collationsForCharsets = $connection->getSupportedCollations();
        ksort($collationsForCharsets, SORT_NATURAL);
        $result = [];
        foreach ($charsetsAndDefaultCollation as $charset => $defaultCollation) {
            $collations = [
                $defaultCollation => $defaultCollation,
            ];

            foreach ($collationsForCharsets as $collation => $forCharset) {
                if ($forCharset === $charset && $collation !== $defaultCollation) {
                    $collations[$collation] = $collation;
                }
            }

            $result[t('Character set: %s', $charset)] = $collations;
        }

        return $result;
    }

    /**
     * @param \Concrete\Core\Database\Connection\Connection $connection
     *
     * @return string
     */
    protected function getConfiguredCollation(Connection $connection): string
    {
        $params = $connection->getParams();
        if (!empty($params['collation'])) {
            return $params['collation'];
        }

        // legacy support
        if (!empty($params['charset'])) {
            $charsetsAndDefaultCollation = $connection->getSupportedCharsets();
            if (isset($charsetsAndDefaultCollation[$params['charset']])) {
                return $charsetsAndDefaultCollation[$params['charset']];
            }
        }

        return '';
    }
}
