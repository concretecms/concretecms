<?php

declare(strict_types=1);

namespace Concrete\Controller\SinglePage\Dashboard\System\Permissions;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Database\Query\LikeBuilder;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Http\ResponseFactory;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Routing\RouterInterface;
use Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface;
use Concrete\Core\Utility\RegexParser;
use Concrete\Core\Utility\RegexParser\ParsedRegex;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class EarlyPageNotFound extends DashboardPageController
{
    public function view(): ?Response
    {
        $config = $this->app->make(Repository::class);
        $this->set('rootUrl', (string) $this->app->make(ResolverManagerInterface::class)->resolve(['/']));
        $this->set('early404Enabled', (bool) $config->get('concrete.early404.enabled'));
        $parser = $this->app->make(RegexParser::class);
        $this->set('early404Rules', array_map(
            function ($regex) use ($parser) {
                try {
                    $parsedRegex = $parser->parseRegEx($regex);
                } catch (\RuntimeException $x) {
                    return (new ParsedRegex(ParsedRegex::TYPE_REGEX, $regex))->jsonSerialize() + ['error' => $x->getMessage()];
                }
                try {
                    $this->checkRegex($parsedRegex);
                } catch (UserMessageException $x) {
                    return $parsedRegex->jsonSerialize() + ['error' => $x->getMessage()];
                }
                return $parsedRegex->jsonSerialize() + ['error' => ''];
            },
            preg_split('/[\r\n]+/', (string) $config->get('concrete.early404.regexes'), -1, \PREG_SPLIT_NO_EMPTY)
        ));

        return null;
    }

    public function test_rule(): JsonResponse
    {
        if (!$this->token->validate('e404-tr')) {
            throw new UserMessageException($this->token->getErrorMessage());
        }
        $parsedRegex = $this->extractParsedRegexFromArray($this->request->request->all());
        $this->checkRegex($parsedRegex);
        $rf = $this->app->make(ResponseFactory::class);

        return $rf->json(true);
    }

    public function test_url(): JsonResponse
    {
        if (!$this->token->validate('e404-tu')) {
            throw new UserMessageException($this->token->getErrorMessage());
        }
        $url = trim((string) $this->request->request->get('url'));
        if ($url === '') {
            throw new UserMessageException(t('Please specify the URL'));
        }
        $url = '/' . $url;
        $parsedRegexes = $this->extractParsedRegexesFromPost();
        $result = [
            'class' => 'text-muted',
            'text' => t('No rules match the specified URL'),
        ];
        foreach ($parsedRegexes as $index => $parsedRegex) {
            if (preg_match($parsedRegex->asRegex(), $url)) {
                $result['class'] = 'text-success';
                $result['text'] = t('The rule #%1$s matches the URL %2$s', $index + 1, $url);
                break;
            }
        }
        $rf = $this->app->make(ResponseFactory::class);

        return $rf->json($result);
    }

    public function set_rules_enabled(): JsonResponse
    {
        if (!$this->token->validate('e404-e')) {
            throw new UserMessageException($this->token->getErrorMessage());
        }
        $enabled = $this->request->request->getBoolean('enabled');
        $config = $this->app->make(Repository::class);
        $config->save('concrete.early404.enabled', $enabled);
        $rf = $this->app->make(ResponseFactory::class);

        return $rf->json($enabled);
    }

    public function save(): JsonResponse
    {
        if (!$this->token->validate('e404-s')) {
            throw new UserMessageException($this->token->getErrorMessage());
        }
        $serialized = [];
        $parsedRegexes = $this->extractParsedRegexesFromPost();
        foreach ($parsedRegexes as $parsedRegex) {
            $this->checkRegex($parsedRegex);
            $serialized[] = $parsedRegex->asRegex();
        }
        $config = $this->app->make(Repository::class);
        $config->save('concrete.early404.regexes', implode("\n", array_unique($serialized)));
        $this->flash('success', t('Settings Saved.'));
        $rf = $this->app->make(ResponseFactory::class);

        return $rf->json(true);
    }

    private function extractParsedRegexesFromPost(): array
    {
        $result = [];
        $all = $this->request->request->all();
        for ($index = 0;; $index++) {
            if (!isset($all['rules'][$index]['type']) && !isset($all['rules'][$index]['text'])) {
                break;
            }
            $result[] = $this->extractParsedRegexFromArray($all['rules'][$index]);
        }

        return $result;
    }

    private function extractParsedRegexFromArray(array $data): ParsedRegex
    {
        $type = $data['type'] ?? null;
        if (!in_array($type, [
            ParsedRegex::TYPE_REGEX,
            ParsedRegex::TYPE_EQUALS,
            ParsedRegex::TYPE_STARTSWITH,
            ParsedRegex::TYPE_ENDSWITH,
            ParsedRegex::TYPE_CONTAINS,
        ], true)) {
            throw new UserMessageException(t('Invalid parameter received: %s', 'type'));
        }

        return new ParsedRegex($type, $data['text'] ?? '', $data['delimiter'] ?? '', $data['modifiers'] ?? '');
    }

    /**
     * @throws \Concrete\Core\Error\UserMessageException
     */
    private function checkRegex(ParsedRegex $parsedRegex): void
    {
        if ($parsedRegex->getText() === '') {
            throw new UserMessageException(t('Please specify the text'));
        }
        $parser = $this->app->make(RegexParser::class);
        try {
            $parser->parseRegEx($parsedRegex->asRegex());
        } catch (\RuntimeException $x) {
            throw new UserMessageException($x->getMessage());
        }
        switch ($parsedRegex->getType()) {
            case ParsedRegex::TYPE_STARTSWITH:
            case ParsedRegex::TYPE_EQUALS:
                if (!str_starts_with($parsedRegex->getText(), '/')) {
                    throw new UserMessageException(t('Requests will always start with the character "%s"', '/'));
                }
                break;
            case ParsedRegex::TYPE_REGEX:
                if (str_starts_with($parsedRegex->getText(), '^') && !str_starts_with($parsedRegex->getText(), '^/')) {
                    throw new UserMessageException(t('Requests will always start with the character "%s"', '/'));
                }
                break;
        }
        if (preg_match($parsedRegex->asRegex(), '/')) {
            throw new UserMessageException(t('This rule would prevent accessing the homepage'));
        }
        if (preg_match($parsedRegex->asRegex(), '/' . DISPATCHER_FILENAME) || preg_match($parsedRegex->asRegex(), '/' . DISPATCHER_FILENAME . '/')) {
            throw new UserMessageException(t('This rule would prevent accessing the website with %s', DISPATCHER_FILENAME));
        }
        // We can't use Doctrine ORM queries, since when using REGEXP/RLIKE (see \Doctrine\DBAL\Platforms\AbstractPlatform::getRegexpExpression()) we have a Syntax Error (it seemd ORM doesn't support regexes)
        $cn = $this->app->make(Connection::class);
        $qb = $cn->createQueryBuilder()
            ->from('PagePaths', 'pp')
            ->select('pp.cPath')
            ->orderBy('pp.cPath')
            ->setMaxResults(1)
        ;
        if ($parsedRegex->getType() === ParsedRegex::TYPE_REGEX) {
            $qb
                ->andWhere('pp.cPath ' . $cn->getDatabasePlatform()->getRegexpExpression() . ' :rx')
                ->setParameter('rx', $parsedRegex->getText())
            ;
        } else {
            $lb = $this->app->make(LikeBuilder::class);
            $qb
                ->andWhere('pp.cPath LIKE :like')
                ->setParameter(
                    'like',
                    $lb->escapeForLike(
                        $parsedRegex->getText(),
                        in_array($parsedRegex->getType(), [ParsedRegex::TYPE_EQUALS, ParsedRegex::TYPE_STARTSWITH], true) ? false : true,
                        in_array($parsedRegex->getType(), [ParsedRegex::TYPE_EQUALS, ParsedRegex::TYPE_ENDSWITH], true) ? false : true
                    )
                )
            ;
        }
        $pagePath = $qb->execute()->fetchOne();
        if ($pagePath !== false) {
            throw new UserMessageException(t('This rule would prevent accessing the existing page at path %s', $pagePath));
        }
        $router = $this->app->make(RouterInterface::class);
        $regex = $parsedRegex->asRegex();
        foreach ($router->getRoutes() as $route) {
            $routePath = $route->getPath();
            if (preg_match($regex, $routePath)) {
                throw new UserMessageException(t('This rule would prevent accessing the route with path %s', $routePath));
            }
        }
    }
}
