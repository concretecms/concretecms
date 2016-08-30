[![Build Status](http://img.shields.io/travis/concrete5/concrete5/develop.svg)](https://travis-ci.org/concrete5/concrete5)
[![Issues Ready To Start](https://badge.waffle.io/concrete5/concrete5.png?label=Accepted:Ready%20to%20start&title=Ready)](https://github.com/concrete5/concrete5/labels/accepted%3Aready%20to%20start)

Welcome to the official repository for concrete5 development! concrete5 is an open source CMS built by people from 
around the world. Want to get involved? Check out our [contributor guide](https://github.com/concrete5/concrete5/blob/develop/CONTRIBUTING.md) for more info.

## Activity
[![Throughput Graph](https://graphs.waffle.io/concrete5/concrete5/throughput.svg)](https://waffle.io/concrete5/concrete5/metrics)

## Documentation

If you're looking for concrete5 documentation, you'll want to navigate over to [documentation.concrete5.org](https://documentation.concrete5.org). 
If you see anything that needs more information or is just completely wrong, contributions are welcomed! 
Just log in to the documentation site with your concrete5.org account and edit away!

## Installation

1. Clone the repository

        git clone https://github.com/concrete5/concrete5.git
        cd concrete5/

2. Use [Composer](https://getcomposer.org/) to install the third party dependencies

        composer install

## Release Cycle

Current release cycle stage: **Beta 2**

### Develop (alpha)
This is the stage of development where we are essentially willing to 
accept any change

#### Expected Contributions:
In this stage we expect the community to be working on new features and 
any and all improvements for concrete5. You should expect to see many
ative discussions about new and existing features as well as many new
pull requests that come in without prior discussion.

#### Accepted Changes:
Accepted changes are the type of pull request we expect to see and make.

* Backwards incompatible changes
* New features
* Feature improvements
* Bug fixes
* Security improvements
* Usability improvements

#### Controversial Changes:
Controversial changes are changes that will need prior discussion and
may not always make it through.

* Huge changes that will break open pull requests
 
### Beta
Once we are in beta stage, things get a little bit more locked down and 
we begin to focus on getting the existing functionality ready for 
release. This stage can last anywhere from a couple of weeks to several
months.

#### Expected Contributions:
Once we hit beta stage, we are beginning down the road to final release.
We expect the community to be testing out:

* Installing
* Upgrading from previous major version (Probably)
* Ways to improve new and existing features
* Usability
* Security

#### Accepted Changes:
* Feature improvements
* Bug fixes
* Security improvements
* Usability improvements

#### Controversial Changes:
* New features
* Backwards incompatible changes (Only applies for the next major release)

### Release Candidate (RC)
The Release Candidate stage is the last stage before release. In this
stage we have a more or less finalized version of concrete5 that will be
released to the public. Essentially any change in this stage is going to
need some level of discussion and scrutiny before being merged. At this
stage we need to be careful not to undo all the testing work that has
been done and we need to be careful not to introduce new bugs.

#### Expected Contributions:
Release candidate phase is very important in that it gives us a final
chance to line up our ducks into their respective rows. During this time
we expect everyone to be testing out existing features and reporting any
bugs.

* Testing existing features
* Testing fixed bugs
* Testing security
* Testing upgrading from all supported upgradable versions

### Accepted Changes:
* Known bug fixes
* Security improvements

### Controversial Changes:
* Feature improvements
* Usability improvements

### Not accepted:
* New features
* Backwards incompatible changes
* Large changes that may introduce bugs


## Legacy

Looking for legacy versions of concrete5? Head over to [the concrete5-legacy repository](http://github.com/concrete5/concrete5-legacy).
