<?php

namespace Concrete\Core\Application\Service\UserInterface\Help;

use HtmlObject\Element;

class HelpPanelMessageFormatter implements MessageFormatterInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Application\Service\UserInterface\Help\MessageFormatterInterface::format($message)
     */
    public function format(Message $message): string
    {
        $section = Element::create('section', $message->getMessageContent());
        $this->addMedia($message, $section);

        return (string) $section;
    }

    protected function createElement(Message $message): Element
    {
        return Element::create('section', $message->getMessageContent());
    }

    protected function addMedia(Message $message, Element $element): void
    {
        $link = (string) $message->getLink();
        $guide = (string) $message->getGuide();
        if ($link !== '' && $guide !== '') {
            $this->addLinkAndGuide($element, $link, $guide);
        } elseif ($link !== '') {
            $this->addLink($element, $link);
        } elseif ($guide !== '') {
            $this->addGuide($element, $guide);
        }
    }

    protected function addLinkAndGuide(Element $element, string $link, string $guide): void
    {
        $element->appendChild(
            Element::create('div', null, ['class' => 'ccm-panel-help-media'])
                ->appendChild(Element::create('div')
                    ->appendChild(
                        Element::create(
                            'a',
                            '<i class="fas fa-play-circle"></i> ' . t('Watch Video'),
                            [
                                'target' => '_blank',
                                'href' => $link,
                            ]
                        )
                    )
                )
                ->appendChild(Element::create('div')
                    ->appendChild(
                        Element::create(
                            'a',
                            t('Run Guide'),
                            [
                                'href' => '#',
                                'data-launch-guide' => $guide,
                            ]
                        )
                    )
                )
        );
    }
    
    protected function addLink(Element $element, string $link): void
    {
        $element->appendChild(
            Element::create('div', null, ['class' => 'ccm-panel-help-media'])
            ->appendChild(Element::create('div')
                ->appendChild(
                    Element::create(
                        'a',
                        '<i class="fas fa-play-circle"></i> ' . t('Watch Video'),
                        [
                            'target' => '_blank',
                            'href' => $link,
                        ]
                        )
                    )
                )
            );
    }
    
    protected function addGuide(Element $element, string $guide): void
    {
        $element->appendChild(
            Element::create('div', null, ['class' => 'ccm-panel-help-media'])
                ->appendChild(Element::create('div')
                    ->appendChild(Element::create('div')
                        ->appendChild(
                            Element::create(
                                'a',
                                '<i class="fas fa-play-circle"></i> ' . t('Run Guide'),
                                [
                                    'class' => 'btn btn-primary',
                                    'href' => '#',
                                    'data-launch-guide' => $guide,
                                ]
                            )
                        )
                    )
                )
        );
    }
}
