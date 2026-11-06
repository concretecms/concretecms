# Instructions for AI tools

These rules apply to any text you write for this repository: pull request descriptions, review comments, issue reports, commit messages, code comments.
Maintainers read everything by hand: reading your text must take less time than reading the diff.

[CONTRIBUTING.md](CONTRIBUTING.md) applies to you too: read it for the AI policy and the coding style rules.

## Pull request descriptions

- At most ~10 lines, with this structure: what changes, why, how to test it.
- Link the issue being fixed (`Fix #123`) instead of describing it again.
- The "why" must come from the human author: if you don't know it, ask for it, don't invent it.
- Do not summarize the diff file by file, and do not repeat what the code already says.
- No lists of benefits, no "Summary"/"Overview"/"Conclusion" sections, no emoji, no praise.
- Mention only what a reviewer can't see from the diff: side effects, backward compatibility breaks, things deliberately left out.

## Comments on pull requests and issues

- One comment per problem, only if it's actionable: say what's wrong and what to do instead.
- No compliments, no paraphrases of the code, no recap of the previous comments.
- When answering a question, answer it in the first sentence.

## Commit messages

- A subject line of at most ~70 characters; add a body only when the reason is not obvious.

## Code comments

- Humans read far more slowly than AI tools: add a comment only when it is needed, and keep it short.
- A comment says why, not what: don't describe what a PHP function does or restate the line that follows.

## Working on the code

- A human must read the code written by an AI tool before it is committed: never commit on your own, wait for the review.
- Every version is developed in its own `<major>.<minor>.x` branch (there is no `main`): branch off the one you are targeting, one topic per pull request.
- The code must run on all the PHP versions allowed by `concrete/composer.json` (`require.php`), starting from the minimum one, which is also set in the root `composer.json` (`config.platform.php`): don't use syntax or functions introduced later.
- Before proposing a fix or a removal, check the open issues and pull requests: it may already be there.
- The core is in `concrete/`: `src` is `Concrete\Core\`, `controllers` is `Concrete\Controller\`; blocks, attributes, authentication types, themes and single pages have their own directories.
- Never edit `concrete/vendor`.
- Don't edit the built assets either: most of the files in `concrete/js`, `concrete/css` and `concrete/bedrock` are generated from the `build` directory (see `build/webpack.mix.js`), which compiles or copies them from `build/assets` and from npm packages, first of all [`@concretecms/bedrock`](https://github.com/concretecms/bedrock) (for example `concrete/js/cms.js`). Read and change their sources instead: a fix to the bedrock code belongs in its own repository.
- To run code inside an installed instance use `concrete/bin/concrete c5:exec script.php` (get services with `app()`). When Concrete is installed the CLI connects to the database while booting, so the database must be running.
- Run a PHPUnit test with `composer run-script test -- tests/tests/Path/To/SomeTest.php` from the repository root (it runs `concrete/vendor/bin/phpunit`).
- Global classes like `\Page`, `\Core`, `\Loader`, `\Database` are aliases and facades listed in `concrete/config/app.php`: facades forward static calls to the underlying service with `__callStatic()`.
- Don't trust the PHPDoc types, read the code that produces the value.
- Many classes extending `Concrete\Core\Foundation\ConcreteObject` return an instance even when the item doesn't exist (for example `Page::getByID()`): such an object is valid only if `isError()` returns a falsy value.
- Many legacy methods return `false`, `null` or nothing on failure.
- The `ConcreteObject` subclasses get their properties from database rows: they are dynamic properties, whose values are usually strings or null.
- Keep the defensive checks unless you verified they can never fail; when a check looks redundant because of a wrong PHPDoc, fix the PHPDoc.
- Read array request parameters with `$request->request->all()['key'] ?? null`: `InputBag::get()` is deprecated for non-scalar values.
- The Doctrine annotation reader used for the entities throws on unknown PHPDoc tags.
- The core is also an API for the code that site owners add on their own: packages installed separately and customizations in the `application` directory (overrides of blocks, views, elements and single pages, custom classes, container bindings, configuration). Code not used by the core isn't necessarily unused, so don't remove public or protected classes, methods, properties or constants just because nothing in this repository references them.
- For the same reason, changing the signature of a public or protected method (parameters, types, return values) is a backward compatibility break even if every caller in the core is updated: avoid it, or say it explicitly in the pull request.
- Code that looks unreachable may still be reached from `concrete/routes`, views or JavaScript bundles: check before removing it, and remove broken code in its own pull request.
