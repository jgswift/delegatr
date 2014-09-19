delegatr
====
PHP 5.5+ delegate system 

[![Build Status](https://travis-ci.org/jgswift/delegatr.png?branch=master)](https://travis-ci.org/jgswift/delegatr)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/jgswift/delegatr/badges/quality-score.png?s=5d6b9d9164025974598868d09842319256fc31be)](https://scrutinizer-ci.com/g/jgswift/delegatr/)
[![Latest Stable Version](https://poser.pugx.org/jgswift/delegatr/v/stable.svg)](https://packagist.org/packages/jgswift/delegatr)
[![License](https://poser.pugx.org/jgswift/delegatr/license.svg)](https://packagist.org/packages/jgswift/delegatr)
[![Coverage Status](https://coveralls.io/repos/jgswift/delegatr/badge.png?branch=master)](https://coveralls.io/r/jgswift/delegatr?branch=master)

## Installation

Install via cli using [composer](https://getcomposer.org/):
```sh
php composer.phar require jgswift/delegatr:0.1.*
```

Install via composer.json using [composer](https://getcomposer.org/):
```json
{
    "require": {
        "jgswift/delegatr": "0.1.*"
    }
}
```

## Dependency

* php 5.5+
* [jgswift/FunctionParser](http://github.com/jgswift/FunctionParser) - forked from [jeremeamia/FunctionParser](http://github.com/jeremeamia/FunctionParser)
* [adlawson/veval.php](http://github.com/adlawson/veval.php) - eval fallback using virtual file system

## Usage

### Built-in Lambda (with Serializable)

One of the cumbersome aspects of closures in php is the need to explicitly 
define the context variables with the "use" statement.  Delegates can make that
process less painful by allowing you to specify an associative array to dynamically
define context.

```php
$x = 10;
$y = 2;

$lambda = new delegatr\Lambda(function() {
    return $x + $y;
}, get_defined_vars());

var_dump($lambda()); // 12
```

```get_defined_vars``` returns a (non-referenced) list of variables defined in the same scope ```get_defined_vars```
is called.  Thus ```$x``` and ```$y``` are dynamically added the the closure's "use" statement
and inserted into the closure scope at run-time.

### Writing Custom Serializable Closures (using trait)

Delegatr doesn't just wrap closures in an object, it uses eval to enable the
serialization of closures - something native php can not do.

The following class uses the Serializable delegate to accomplish this.  
```php
class MyDelegate {
    use delegatr\Serializable;
}

$delegate = new MyDelegate(function() {
    return 'foo';
});

$serial_string = serialize($delegate);

$delegate2 = unserialize($serial_string)

var_dump($delegate2()); // returns 'foo';
```

Using the above trait prevents delegatr from imposing on your domain but if that 
isn't a concern just instantiate or inherit the Lambda class as shown below.

```php
$delegate = new delegatr\Lambda(function() {
    return 'foo';
});

class MyLambda extends delegatr\Lambda {
    /* ... */
}
```

### Simple delegate (without serialization)

A simpler implementation is also available without \Serializable included

```php
class MyDelegate {
    use delegatr\Delegate;
}

$delegate = new MyDelegate(function() {
    return 'foo';
});

var_dump($delegate()); // returns 'foo';
```

### Eval Fallback & Vulnerability

The [eval](http://php.net/manual/en/function.eval.php) function is heavily relied on in this package.  
If you are not comfortable with eval or do not understand the security risks, I do not suggest you use this package.

However, *eval* itself is *not* required for this package.  
Delegatr uses [adlawson/veval.php](http://github.com/adlawson/veval.php) to compile scripts at run-time even in environments where *eval* is disabled.
Bypassing eval in this way doesn't reduce the risk of code injection.




