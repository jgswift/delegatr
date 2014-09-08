delegatr
====
PHP 5.5+ delegate system 

[![Build Status](https://travis-ci.org/jgswift/delegatr.png?branch=master)](https://travis-ci.org/jgswift/delegatr)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/jgswift/delegatr/badges/quality-score.png?s=5d6b9d9164025974598868d09842319256fc31be)](https://scrutinizer-ci.com/g/jgswift/delegatr/)

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

## Usage

### Serializable Closure

Delegatr provides a substitute for closures, but with the additional ability to serialize delegates

The following is a serializable Delegate minimal example
```php
<?php
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

### Simple delegate (without serialization)

A simpler implementation is also available without \Serializable included

```php
<?php
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

However, *eval* itself is *not* required to serialize delegates.  
Delegatr uses [adlawson/veval.php](http://github.com/adlawson/veval.php) to compile scripts at run-time even in environments where *eval* is disabled.
Bypassing eval in this way doesn't reduce the risk of code injection.




