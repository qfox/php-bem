php-bem
=======

BEM render library usign v8js extension (https://github.com/preillyme/v8js)

Based on https://github.com/bem/bem-bl-test.git



How to start?
-------------

That class requires on php5.3+ and v8js extension.

### What you need to use it?
```bash
$ sudo pecl install v8js
```

When you finished you can verify your new extension installation:
```bash
$ php -m |grep v8js
v8js
```

### Let's get BEM for php!

Clone repository to some place on your pc and prepare bem to use:
```bash
cd ~
git clone git://github.com/zxqfox/php-bem.git
cd bem-php
pushd .; cd examples/www; make; popd
```

Done. Let's try it now!
```bash
php phpbem/test.php
```

If you don't see some output please contact me with issues ^_^


How to use it
-------------

```php

// include class
require "./phpbem.php";
use \bem\bemhtml;

// and use it like that:
$bem = new bemhtml($rootpath, $options = array());
$bem->page($pathToBlock)->render($context, array $env, string $entrypoint = 'render', $json = false);

// or like that:
$bem = new bemhtml($rootpath, $options = array());
$bem->page($pathToBlock, [$block.'.'.$tech.'.js']);
$bemjson = $bem->json(array $context, array $env, 'render');
echo $bem->render($bemjson);
```
