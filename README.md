delegatr
====
PHP 5.4+ delegate system

[![Build Status](https://travis-ci.org/jgswift/delegatr.png?branch=master)](https://travis-ci.org/jgswift/delegatr)

## Installation

Install via [composer](https://getcomposer.org/):
```sh
php composer.phar require jgswift/delegatr:dev-master
```

## Usage

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

Note: this package relies heavily on eval to function.  If you are not comfortable with using eval or do not understand the security risks, I do not suggest you use this package.