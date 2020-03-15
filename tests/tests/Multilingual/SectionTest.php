<?php
namespace Concrete\Tests\Multilingual;

use Concrete\Core\Multilingual\Page\Section\Section;
use Concrete\TestHelpers\Page\PageTestCase;

class SectionTest extends PageTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->app->make('cache/request')->disable();

        // get entity manager from database connection
        $em = $this->connection()->getEntityManager();

        // initialize locale service
        $service = new \Concrete\Core\Localization\Locale\Service($em);

        // get current site
        $site = $this->app->make('site')->getSite();

        // get page template "full"
        $template = \Concrete\Core\Page\Template::getByHandle('full');

        // add locale with home page
        $locale = $service->add($site, 'de', 'CH');
        $service->addHomePage($locale, $template, 'Second language', 'chde');

        // refresh site entity from the database
        $em->refresh($site);
    }

    public function testGetByLocale()
    {
        // load second language section with locale
        $section = \Concrete\Core\Multilingual\Page\Section\Section::getByLocale('de_CH');

        // check section
        $this->assertNotFalse($section, 'Unable to load Section by locale');
        $this->assertEquals('de_CH', $section->getLocale());
    }

    public function testGetByLanguage()
    {
        // load second language section with language
        $section = \Concrete\Core\Multilingual\Page\Section\Section::getByLanguage('de');

        // check section
        $this->assertNotFalse($section, 'Unable to load Section by language');
        $this->assertEquals('de_CH', $section->getLocale());
    }

    public function testMultilingualEnabled()
    {
        $enabled = $this->app->make('multilingual/detector')->isEnabled();
        $this->assertTrue($enabled);
    }

    /**
     * This test will check if c5 can duplicate a page on the multilingual site correctly.
     *
     * @see https://github.com/concrete5/concrete5/issues/7430
     */
    public function testRegisterDuplicate()
    {
        $default = Section::getDefaultSection();
        $second = Section::getByLocale('de_CH');

        // Create two pages in the same language section
        $oldPage = self::createPage('Old Page', $default);
        $newPage = $oldPage->duplicate($default);

        // When duplicating a page within the same language tree, a new mpRelationID should created for a new page.
        $this->assertNotEquals(
            Section::getMultilingualPageRelationID($oldPage->getCollectionID()),
            Section::getMultilingualPageRelationID($newPage->getCollectionID()),
            'A page duplicated in the same locale, but did not created new mpRelationID.'
        );

        // Create a page in the second locale
        $newPageInSecondLocale = $oldPage->duplicate($second);

        // If you duplicate a page into a different language tree, it should get the same mpRelationID.
        $this->assertEquals(
            Section::getMultilingualPageRelationID($oldPage->getCollectionID()),
            Section::getMultilingualPageRelationID($newPageInSecondLocale->getCollectionID()),
            'Two pages are related between two locales, but we could not get same mpRelationID.'
        );

        $oldPage->delete();
        $newPage->delete();
        $newPageInSecondLocale->delete();
    }
}
