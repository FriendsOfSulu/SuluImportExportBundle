##Requirements

* PHP >= 7.3 
* Sulu 2.*
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

IMPORT_DIR='var/import/'
EXPORT_DIR='var/export/'
 ```



##UPGRADE

###1.1.1

Since version 1.1.1 it should be possible to change the path of the export and import easily.
For this the ENV must be extended by the following variables.
To map the same function as in the 1.0.0 version it is sufficient to leave the variables empty.

 ```dotenv
IMPORT_DIR=
EXPORT_DIR=
 ```