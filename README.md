# TsvDirectory
Модуль для управления справочниками приложения, привязанный к системе администрирования ZfcAdmin.

## Requirements
* [Zend Framework 2](https://github.com/zendframework/zf2) 
* [ZfcAdmin](https://github.com/ZF-Commons/ZfcAdmin) 
* [bjyoungblood/BjyProfiler](https://github.com/bjyoungblood/BjyProfiler) Db driver 
* [bjyoungblood/BjyAuthorize](https://github.com/bjyoungblood/BjyAuthorize) Auth system

## Features
* Theme based on [Twitter Bootstrap](http://twitter.github.com/bootstrap/)

## Installation

 -. Add `"marks12/tsv-directory": "dev-master"` to your `composer.json` file and run `php composer.phar update`.
 -. Add 'DoctrineModule','DoctrineORMModule','ZfcAdmin','Tsvdir to your `config/application.config.php` file under the `modules` key after `ZfcAdmin`.
 -. Create directory  data/DoctrineORMModule/Proxy  and make sure your application has write access to it.
 -. Copy or create a symlink of public/css, public/js and public/images to your website root directory

## Configuration 

Tsvdir - Модуль управления справочниками приложения
