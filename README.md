MremiTemplatingExtraBundle
==========================

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/6f168569-7975-4b8a-bc15-c79446ba0fef/big.png)](https://insight.sensiolabs.com/projects/6f168569-7975-4b8a-bc15-c79446ba0fef)

[![Build Status](https://api.travis-ci.org/mremi/TemplatingExtraBundle.png?branch=master)](https://travis-ci.org/mremi/TemplatingExtraBundle)
[![Total Downloads](https://poser.pugx.org/mremi/templating-extra-bundle/downloads.png)](https://packagist.org/packages/mremi/templating-extra-bundle)
[![Latest Stable Version](https://poser.pugx.org/mremi/templating-extra-bundle/v/stable.png)](https://packagist.org/packages/mremi/templating-extra-bundle)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/mremi/TemplatingExtraBundle/badges/quality-score.png?s=2aa12928b5c08db31bd00032cd4f4e336f7f3950)](https://scrutinizer-ci.com/g/mremi/TemplatingExtraBundle/)
[![Code Coverage](https://scrutinizer-ci.com/g/mremi/TemplatingExtraBundle/badges/coverage.png?s=d26b08bad01be61e7c6660953d9eb296cf8309a7)](https://scrutinizer-ci.com/g/mremi/TemplatingExtraBundle/)

This bundle profiles all the rendered templates (Twig or PHP) during a Symfony2
page rendering. This only includes templates which are rendered by `render` and
`renderResponse` through the templating service (for instance, `include` and
`embed` Twig tags are not tracked).

## License

This bundle is available under the [MIT license](Resources/meta/LICENSE).

## Prerequisites

This version of the bundle requires Symfony 2.1+.

**Basic Docs**

* [Installation](#installation)
* [Profiler](#profiler)
* [Contribution](#contribution)

<a name="installation"></a>

## Installation

Installation is a quick 2 step process:

1. Download MremiTemplatingExtraBundle using composer
2. Enable the Bundle

### Step 1: Download MremiTemplatingExtraBundle using composer

Add MremiTemplatingExtraBundle in your composer.json:

```js
{
    "require": {
        "mremi/templating-extra-bundle": "dev-master"
    }
}
```

Now tell composer to download the bundle by running the command:

``` bash
$ php composer.phar update mremi/templating-extra-bundle
```

Composer will install the bundle to your project's `vendor/mremi` directory.

### Step 2: Enable the bundle

Enable the bundle in the kernel:

``` php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new Mremi\TemplatingExtraBundle\MremiTemplatingExtraBundle(),
    );
}
```

<a name="profiler"></a>

## Profiler

If your are in debug mode (see your front controller), you can check in the web
debug toolbar the rendered templates and some statistics from the current
HTTP request: number of templates, consumed memory, request duration...

It's very easy to know which templates consume just looking at the colors (red
and yellow).

Moreover, you can see all parameters passed to each template. This can be
useful for the front office development.

![Screenshot](https://raw.github.com/mremi/TemplatingExtraBundle/master/Resources/doc/images/profiler.png)

If you configured the [framework bundle](http://symfony.com/doc/current/reference/configuration/framework.html#ide)
(or `xdebug.file_link_format`), you can edit templates just by clicking on name.

<a name="contribution"></a>

## Contribution

Any question or feedback? Open an issue and I will try to reply quickly.

A feature is missing here? Feel free to create a pull request to solve it!

I hope this has been useful and has helped you. If so, share it and recommend
it! :)

[@mremitsme](https://twitter.com/mremitsme)
