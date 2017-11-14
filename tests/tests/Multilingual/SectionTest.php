<?php

class SectionTest extends PageTestCase
{
    public function setUp()
    {
        parent::setUp();

        // get entity manager from database connection
        $em = $this->connection()->getEntityManager();

        // initialize locale service
        $service = new \Concrete\Core\Localization\Locale\Service($em);

        // get current site
        $site = \Core::make('site')->getSite();

        // get page template "full"
        $template = \Concrete\Core\Page\Template::getByHandle('full');

        // add locale with home page
        $locale = $service->add($site, 'de', 'CH');
        $service->addHomePage($locale, $template, 'Second language', 'chde');
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
}
