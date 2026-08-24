<?php

declare(strict_types=1);

namespace Concrete\Tests\Block;

use Concrete\Core\Attribute\Category\CategoryService as AttributeCategoryService;
use Concrete\Core\Entity\Attribute\Category as AttributeCategory;
use Concrete\Core\Entity\Attribute\Key\ExpressKey;
use Concrete\Core\Entity\Attribute\Type as AttributeType;
use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Entity\Express\Form;
use Concrete\TestHelpers\Block\BlockApiValueTestCase;
use Doctrine\ORM\EntityManagerInterface;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * Tests the API representation of the block that renders an Express form.
 *
 * The value of the block is the record of its table, which is what the API exposes anyway: only its schema
 * is written by the controller.
 *
 * @see \Concrete\Block\ExpressForm\Controller::getApiValueSchema()
 */
class ExpressFormApiValueTest extends BlockApiValueTestCase
{
    /**
     * The Express form rendered by the block.
     *
     * @var \Concrete\Core\Entity\Express\Form|null
     */
    private $form;

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\TestHelpers\Block\BlockApiValueTestCase::getEntityClassNames()
     */
    protected function getEntityClassNames(): array
    {
        return array_merge(parent::getEntityClassNames(), [
            AttributeCategory::class,
            AttributeType::class,
            Entity::class,
            Entry::class,
            ExpressKey::class,
            Form::class,
        ]);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\TestHelpers\Database\ConcreteDatabaseTestCase::getTables()
     */
    protected function getTables()
    {
        return array_merge(parent::getTables(), [
            // the export() method of the block looks for the file set the uploaded files are added to
            'FileSets',
        ]);
    }

    public function setUp(): void
    {
        parent::setUp();
        $this->form = null;
    }

    public function testTheEntityOfTheFormIsExposedTogetherWithIt(): void
    {
        $block = $this->addBlock();

        $value = $this->getApiValue($block);
        static::assertSame((string) $this->getForm()->getID(), $value['exFormID']);
        // the block doesn't store the entity: it's the one the form belongs to
        static::assertSame((string) $this->getForm()->getEntity()->getID(), $value['exEntityID']);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\TestHelpers\Block\BlockApiValueTestCase::getBlockTypeHandle()
     */
    protected function getBlockTypeHandle(): string
    {
        return 'express_form';
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\TestHelpers\Block\BlockApiValueTestCase::getSaveData()
     */
    protected function getSaveData(): array
    {
        // that's what the form of the block sends when the Express form already exists
        return [
            'exFormID' => $this->getForm()->getID(),
            'submitLabel' => 'Send the form, please',
            'thankyouMsg' => 'Thank you a lot ;)',
            'displayCaptcha' => 1,
            'storeFormSubmission' => 1,
            'notifyMeOnSubmission' => 1,
            'recipientEmail' => 'jane@doe.com,jonh@doe.com',
            'replyToEmailControlID' => null,
            'redirectCID' => 0,
            'addFilesToSet' => 0,
            'addFilesToFolder' => 0,
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\TestHelpers\Block\BlockApiValueTestCase::getExpectedApiValue()
     */
    protected function getExpectedApiValue(): array
    {
        // the keys are in the order of the columns of the btExpressForm table, with the ID of the entity
        // added by the export() method of the block
        return [
            'exFormID' => (string) $this->getForm()->getID(),
            'submitLabel' => 'Send the form, please',
            'thankyouMsg' => 'Thank you a lot ;)',
            'notifyMeOnSubmission' => '1',
            'recipientEmail' => 'jane@doe.com,jonh@doe.com',
            'displayCaptcha' => '1',
            'storeFormSubmission' => '1',
            'redirectCID' => '0',
            'replyToEmailControlID' => null,
            'addFilesToSet' => '0',
            'addFilesToFolder' => '0',
            'exEntityID' => (string) $this->getForm()->getEntity()->getID(),
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\TestHelpers\Block\BlockApiValueTestCase::hasCustomApiValue()
     */
    protected function hasCustomApiValue(): bool
    {
        return true;
    }

    /**
     * Get the Express form rendered by the block (it's created the first time it's asked for).
     */
    private function getForm(): Form
    {
        if ($this->form === null) {
            $categoryService = $this->app->make(AttributeCategoryService::class);
            if ($categoryService->getByHandle('express') === null) {
                // the Express entities hold the attributes of their entries
                $categoryService->add('express');
            }
            $entityManager = $this->app->make(EntityManagerInterface::class);
            // the tests of a class share the tables: the entity may have been created by another one
            $entity = $entityManager->getRepository(Entity::class)->findOneBy(['handle' => 'contact_request']);
            if ($entity === null) {
                $entity = new Entity();
                $entity->setName('Contact Request');
                $entity->setHandle('contact_request');
                $entity->setPluralHandle('contact_requests');
                $entity->setEntityResultsNodeId(0);
                $form = new Form();
                $form->setName('Contact Form');
                $form->setEntity($entity);
                $entity->getForms()->add($form);
                $entityManager->persist($entity);
                $entityManager->flush();
            }
            $this->form = $entity->getForms()->first();
        }

        return $this->form;
    }
}
