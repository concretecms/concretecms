# JShrink [![Build Status](https://travis-ci.org/tedivm/JShrink.png?branch=master)](https://travis-ci.org/tedivm/JShrink)

[![Latest Stable Version](https://poser.pugx.org/tedivm/JShrink/v/stable.png)](https://packagist.org/packages/tedivm/JShrink)
[![Total Downloads](https://poser.pugx.org/tedivm/JShrink/downloads.png)](https://packagist.org/packages/tedivm/JShrink)


JShrink is a php class that minifies javascript so that it can be delivered to the client quicker. This code can be used
by any product looking to minify their javascript on the fly (although caching the results is suggested for performance
reasons). Unlike many other products this is not a port into php but a native application, resulting in better
performance.


## Usage

Minifying your code is simple call to a static function-

````
<?php
// Basic (default) usage.
$minifiedCode = JShrink\Minifier::minify($js);

// Disable YUI style comment preservation.
$minifiedCode = JShrink\Minifier::minify($js, array('flaggedComments' => false));
````


## Results

* Raw - 586,990
* Gzip - 151,301
* JShrink - 371,982
* JShrink and Gzip - 93,507


## Installing

### Composer

Installing JShrink can be done through a variety of methods, although Composer is
recommended.

Until JShrink reaches a stable API with version 1.0 it is recommended that you
review changes before even Minor updates, although bug fixes will always be
backwards compatible.

```
"require": {
  "tedivm/jshrink": "0.5.*"
}
```

### Pear

JShrink is also available through Pear.

```
$ pear channel-discover pear.tedivm.com
$ pear install tedivm/JShrink
```


### Github

Releases of JShrink are available on [Github](https://github.com/tedivm/JShrink/releases).


## License

JShrink is licensed under the BSD License. See the LICENSE file for details.

In the spirit of open source, use of this library for evil is discouraged but not prohibited.