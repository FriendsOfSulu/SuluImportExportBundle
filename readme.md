##Requirements

* PHP 7.3
* Sulu 2.0.*
* Symfony 4.4 or 5.*


### Install the bundle 

Execute the following [composer](https://getcomposer.org/) command to add the bundle to the dependencies of your 
project:

```bash

composer require thecadien/sulu-import-export-bundle

```

### Enable the bundle 
 
 Enable the bundle by adding it to the list of registered bundles in the `config/bundles.php` file of your project:
 
 ```php
 return [
     /* ... */
     TheCadien\Bundle\SuluImportExportBundle\SuluImportExportBundle::class => ['all' => true],
 ];
 ```

### Configure the bundle 

Configure arguments in your `.env` with your Database Config like the following way.

 ```dotenv
DATABASE_HOST='host'
DATABASE_USER='user'
DATABASE_PASSWORD='password'
DATABASE_NAME='db-name'
MEDIA_PATH='var/uploads/media'
 ```