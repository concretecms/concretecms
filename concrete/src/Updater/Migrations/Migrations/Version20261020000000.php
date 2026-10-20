<?php

declare(strict_types=1);

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Sharing\SocialNetwork\LinkUrlValidator;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * Clean up the values of the social_links attributes: older versions stored them HTML-encoded and without
 * checking that they were URLs, and the links are now rendered without further checks.
 */
class Version20261020000000 extends AbstractMigration implements RepeatableMigrationInterface
{
    public function upgradeDatabase()
    {
        $validator = $this->app->make(LinkUrlValidator::class);
        $schemes = [];
        foreach ($validator->getAllowedSchemes() as $scheme) {
            // Escape the regex metacharacters allowed in a scheme in a way that works with every MySQL/MariaDB regex engine
            $schemes[] = preg_replace('/[+.\-]/', '[$0]', $scheme);
        }
        $this->connection->executeStatement(
            'DELETE FROM atSelectedSocialLinks WHERE serviceInfo IS NULL OR LOWER(serviceInfo) NOT REGEXP ?',
            ['^(' . implode('|', $schemes) . '):']
        );
        // Only the rows that may fail the validation: characters not allowed in URLs, and & (HTML entities)
        $rows = $this->connection->fetchAllAssociative(
            'SELECT avsID, serviceInfo FROM atSelectedSocialLinks WHERE serviceInfo REGEXP ?',
            ["[^a-zA-Z0-9._~:/?#@!$'()*+,;=%-]"]
        );
        foreach ($rows as $row) {
            $url = html_entity_decode($row['serviceInfo'], ENT_QUOTES, 'UTF-8');
            if (!$validator->isValid($url)) {
                $this->connection->delete('atSelectedSocialLinks', ['avsID' => $row['avsID']]);
            } elseif ($url !== $row['serviceInfo']) {
                $this->connection->update('atSelectedSocialLinks', ['serviceInfo' => $url], ['avsID' => $row['avsID']]);
            }
        }
    }
}
