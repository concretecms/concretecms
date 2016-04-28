
#  How to Contribute to Concrete5 Core Code

Community contributions are one of the key points that make open source software so great.
And concrete5 is no exception, so we'd like to thank you for your help.

## What We'd Love Help On

We've tried to categorize our issues in ways that will make it clear how best we can use your help. In general, if something is labelled as "accepted:ready to start" it is ready for development! If you find an issue that is labeled in this way, without a dedicated person assigned to it, post that you'd like to work on it, and go for it. When you're done submit a pull request and we'll review it right away for inclusion.

We have a lot of items we'd like help on: if you find an issue tagged as "accepted:ready to start" **and** either "priority:like to have" or "priority:love to have" you'll know that this is something we'd really, really like to get. We'd love the assistance.

Helpful Links:

* [Issues ready to be worked on](https://github.com/concrete5/concrete5/issues?q=is%3Aopen+is%3Aissue+label%3A%22accepted%3Aready+to+start%22+no%3Aassignee)
* [New proposals](https://github.com/concrete5/concrete5/issues?q=is%3Aopen+is%3Aissue+label%3A"type%3Arfc")
* [Things under discussion](https://github.com/concrete5/concrete5/issues?q=is%3Aopen+is%3Aissue+label%3Atype%3Adiscussion)

## Pull Requests

Before submitting pull requests, please be sure to read these pages:

- Adopting a common coding style makes developers life easier and may be helpful in finding some kind of bugs. Furthermore, like @aembler once said, we have to be good citizens: speaking the same dialect is useful for everybody. So, please be sure to consult the [Coding-Style Guidelines](http://www.concrete5.org/documentation/developers/5.7/background/coding-style-guidelines).

  One easy, secure and fast way to fix coding style problems is using [php-cs-fixer](http://cs.sensiolabs.org/). To fix a file simply call

  `php-cs-fixer --config-file==<webroot>/.php_cs fix <filename>`

  Please run the above command on every PHP-only file included in your pull requests (preferably in a separated commit).

- Pull requests that address existing issues are much **much** more likely to be accepted than unsolicited pull requests. 

- Pull requests should normally be directed at the `develop` branch. Master is reserved for the most recent stable release. [Read more about the gitflow branching model](https://github.com/concrete5/concrete5/issues/1058).

- If you used to develop for concrete5 Legacy (v5.6 and older), you should read the [Migration Guide](https://github.com/concrete5/concrete5/wiki/Migration-Guide): it explains some of the main differences introduced in concrete5 5.7 and later.

## Github issues vs Bug tracker

Github is **not** the place for unconfirmed bugs. If this is an unconfirmed bug (and almost all of them are), they should be posted to the [official concrete5 bug tracker](http://www.concrete5.org/developers/bugs) so that they can be confirmed and voted on. Just because something is posted here does not guarantee it will be placed into a release milestone. **Exceptions** to this rule include issues that are accompanied by a pull request, but even a pull request isn't guaranteed to be accepted into the core.

Thanks everyone!
