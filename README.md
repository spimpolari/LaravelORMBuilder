# Laravel ORM Builder


[![Software License][ico-license]](LICENSE.md)


Reverse Database into Eloquent ORM Model


## Installation

Via Composer

``` bash
$ composer require spimpolari/LaravelORMBuilder
```

Then add the service provider in `config/app.php`:

```php
spimpolari\LaravelORMBuilder\ORMBuilderServiceProvider::class,
```
Modify .env config file with database access info


## db
**Command:**
```bash
$ php artisan orm:db
```
This command reverse all table from database into `app/Model` Eloquent ORM Model with file `Tablename.php` and default option.

**Argument:**
```bash
$ php artisan orm:db app/Model
```
Path of Model to write all Eloquent ORM Model from start Laravel root directory.

```bash
$ php artisan orm:db app/Model App\Model
```
Namespace of ORM Model.

**Options:**
```bash
$ php artisan orm:db -F/--force
```
Force Builder to overwrite all exists model without any question.

```bash
$ php artisan orm:db -O/--only=table1,table2,table3
```
List alla table you want to convert, if set, exclude option will be ignored. 

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.


## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CONDUCT](CONDUCT.md) for details.


## Credits

- [Stefano Pimpolari][link-author]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/spimpolari/ORMBuilder.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/spimpolari/ORMBuilder/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/spimpolari/ORMBuilder.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/spimpolari/ORMBuilder.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/spimpolari/ORMBuilder.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/spimpolari/ORMBuilder
[link-travis]: https://travis-ci.org/spimpolari/ORMBuilder
[link-scrutinizer]: https://scrutinizer-ci.com/g/spimpolari/ORMBuilder/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/spimpolari/ORMBuilder
[link-downloads]: https://packagist.org/packages/spimpolari/ORMBuilder
[link-author]: https://github.com/spimpolari
[link-contributors]: ../../contributors
