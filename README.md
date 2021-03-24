[![Build Status](https://travis-ci.com/zoparga/laravel-szamlazzhu.svg?branch=master)](https://travis-ci.com/zoparga/laravel-szamlazzhu)
[![Latest Stable Version](https://poser.pugx.org/zoparga/laravel-szamlazzhu/version)](https://packagist.org/packages/zoparga/laravel-szamlazzhu)
[![Latest Stable Version](https://poser.pugx.org/zoparga/laravel-szamlazzhu/downloads)](https://packagist.org/packages/zoparga/laravel-szamlazzhu)
[![License](https://poser.pugx.org/zoparga/laravel-szamlazzhu/license)](https://packagist.org/packages/zoparga/laravel-szamlazzhu)

# laravel-szamlazzhu
Implementation for Laravel which was made to support Szamlazz.hu Agent version of **3.4** and higher.

# Original
## Based on SzuniSOFT / laravel-szamlazzhu
### https://github.com/SzuniSOFT/laravel-szamlazzhu
composer require SzuniSOFT/laravel-szamlazzhu

I copied & published this package, because I needed Laravel 8 support, 
which is require GuzzleHttp 7 & the maintainers don't want to add it yet.



## Installation
```bash
composer require zoparga/laravel-szamlazzhu
```

## Introduction
This package supports the following methods:
- Invoice creation
- Invoice cancellation (reversing)
- Proforma invoice creation
- Proforma invoice deletion
- Receipt creation
- Receipt cancellation (reversing)

## Automatic PDF storing
Package can automatically save generated PDF files and store on the given disk. For further information about the configuration possibilities please refer to the [configuration](doc/technical/config.md) documentation.

## Requirements
Supports Laravel: **5.5** / **6.*** / **7.*** / **8.***

## Documentations
For usage please refer to the [technical documentation](doc/technical/documentation.md).

The [official API](doc/official/Technical_Documentation_invoicing.pdf) documentation is also accessible _(in PDF format)_ in this repository.
