<?php

namespace Concrete\Controller\SinglePage\Dashboard\System\Registration;

use Concrete\Core\Page\Controller\DashboardSitePageController;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\Single;

class Profiles extends DashboardSitePageController
{
    public function view()
    {
        $config = $this->getSite()->getConfigRepository();

        $this->set('publicProfiles', (bool) $config->get('user.profiles_enabled'));
        $this->set('displayAccountMenu', (bool) $config->get('user.display_account_menu'));
        $this->set('gravatarFallback', (bool) $config->get('user.gravatar.enabled'));
        $gravatarMaxLevels = $this->getAvailableGravatarLevels();
        $gravatarMaxLevel = $config->get('user.gravatar.max_level');
        if (!isset($gravatarMaxLevels[$gravatarMaxLevel])) {
            $gravatarMaxLevel = key($gravatarMaxLevels);
        }
        $this->set('gravatarMaxLevel', $gravatarMaxLevel);
        $this->set('gravatarMaxLevels', $gravatarMaxLevels);
        $gravatarImageSets = $this->getAvailableGravatarImageSets();
        $gravatarImageSet = $config->get('user.gravatar.image_set');
        if (!isset($gravatarImageSets[$gravatarImageSet])) {
            $gravatarImageSet = key($gravatarImageSets);
        }
        $this->set('gravatarImageSet', $gravatarImageSet);
        $this->set('gravatarImageSets', $gravatarImageSets);
    }

    public function update_profiles()
    {
        $config = $this->getSite()->getConfigRepository();
        $post = $this->request->request;
        if (!$this->token->validate('update_profile')) {
            $this->error->add('Invalid Token.');
        }
        $publicProfiles = (bool) $post->get('public_profiles');
        $isPublicProfilesChanged = (bool) $config->get('user.profiles_enabled') !== $publicProfiles;
        $gravatarFallback = (bool) $post->get('gravatar_fallback');
        if ($gravatarFallback) {
            $gravatarMaxLevel = (string) $post->get('gravatar_max_level');
            if (!array_key_exists($gravatarMaxLevel, $this->getAvailableGravatarLevels())) {
                $this->error->add(t('Please specify the maximum Gravatar rating.'));
            }
            $gravatarImageSet = (string) $post->get('gravatar_image_set');
            if (!array_key_exists($gravatarImageSet, $this->getAvailableGravatarImageSets())) {
                $this->error->add(t('Please specify the Gravatar image set.'));
            }
        }
        if ($this->error->has()) {
            return $this->view();
        }
        $config->save('user.profiles_enabled', $publicProfiles);
        $config->save('user.display_account_menu', (bool) $post->get('display_account_menu'));
        $config->save('user.gravatar.enabled', $gravatarFallback);
        if ($gravatarFallback) {
            $config->save('user.gravatar.max_level', $gravatarMaxLevel);
            $config->save('user.gravatar.image_set', $gravatarImageSet);
        }
        if ($isPublicProfilesChanged) {
            if ($publicProfiles) {
                $this->enablePublicProfiles();
                $this->flash('success', t('Public profiles have been enabled. Public profile and directory single pages added to the sitemap.'));
            } else {
                $this->disablePublicProfiles();
                $this->flash('success', t('Public profiles have been disabled. Public profile and directory single pages removed from the sitemap'));
            }
        } else {
            $this->flash('success', t('Public profiles settings have been updated.'));
        }

        return $this->buildRedirect($this->action());
    }

    protected function enablePublicProfiles(): void
    {
        Single::add('/members');
        Single::add('/members/profile')->update(['cName' => 'View Profile']);
        Single::add('/members/directory');
    }

    protected function disablePublicProfiles(): void
    {
        foreach ($this->app->make('site')->getList() as $site) {
            foreach (['/members/directory', '/members/profile', '/members'] as $path) {
                $c = Page::getByPath($path, 'RECENT', $site);
                if ($c && !$c->isError()) {
                    $c->delete();
                }
            }
        }
    }

    protected function getAvailableGravatarLevels(): array
    {
        return [
            'g' => tc(/*i18n: %s is a level code */'GravatarRating', '%s: suitable for display on all websites with any audience type', 'G'),
            'pg' => tc(/*i18n: %s is a level code */'GravatarRating', '%s: may contain rude gestures, provocatively dressed individuals, the lesser swear words, or mild violence', 'PG'),
            'r' => tc(/*i18n: %s is a level code */'GravatarRating', '%s: may contain such things as harsh profanity, intense violence, nudity, or hard drug use', 'R'),
            'x' => tc(/*i18n: %s is a level code */'GravatarRating', '%s: may contain hardcore sexual imagery or extremely disturbing violence', 'X'),
        ];
    }

    protected function getAvailableGravatarImageSets(): array
    {
        return [
            '404' => tc(/*i18n: %s is a set code */'GravatarImageSets', '%s: do not load any image if none is associated with the email hash, instead return an HTTP 404 (File Not Found) response', '404'),
            'mp' => tc(/*i18n: %s is a set code */'GravatarImageSets', '%s: a simple, cartoon-style silhouetted outline of a person (does not vary by email hash)', 'mp'),
            'identicon' => tc(/*i18n: %s is a set code */'GravatarImageSets', '%s: a geometric pattern based on an email hash', 'identicon'),
            'monsterid' => tc(/*i18n: %s is a set code */'GravatarImageSets', "%s: a generated 'monster' with different colors, faces, etc", 'monsterid'),
            'wavatar' => tc(/*i18n: %s is a set code */'GravatarImageSets', '%s: generated faces with differing features and backgrounds', 'wavatar'),
            'retro' => tc(/*i18n: %s is a set code */'GravatarImageSets', '%s: awesome generated, 8-bit arcade-style pixelated faces', 'retro'),
            'robohash' => tc(/*i18n: %s is a set code */'GravatarImageSets', '%s: a generated robot with different colors, faces, etc', 'robohash'),
            'blank' => tc(/*i18n: %s is a set code */'GravatarImageSets', '%s: a transparent PNG image', 'blank'),
        ];
    }
}
