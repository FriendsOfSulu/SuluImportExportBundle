##Requirements

* PHP 7.3
* Sulu 2.0.*
* Symfony 4.3


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

Configure the Commands in your `config/services.yaml`

 ```yaml
TheCadien\Bundle\SuluImportExportBundle\Command\ExportCommand:
    tags: [name: 'console.command']
    arguments:
        $databaseHost: '%env(DATABASE_HOST)%'
        $databaseUser: '%env(DATABASE_USER)%'
        $databaseName: '%env(DATABASE_NAME)%'
        $databasePassword: '%env(DATABASE_PASSWORD)%'
        $exportDirectory: '%kernel.project_dir%'
        $uploadsDirectory: '%env(MEDIA_PATH)%'

TheCadien\Bundle\SuluImportExportBundle\Command\ImportCommand:
    tags: [name: 'console.command']
    arguments:
        $databaseHost: '%env(DATABASE_HOST)%'
        $databaseUser: '%env(DATABASE_USER)%'
        $databaseName: '%env(DATABASE_NAME)%'
        $databasePassword: '%env(DATABASE_PASSWORD)%'
        $importDirectory: '%kernel.project_dir%'
        $uploadsDirectory: '%env(MEDIA_PATH)%'
 ```

Configure arguments in your `.env` with your Database Config.

 ```dotenv
DATABASE_HOST='host'
DATABASE_USER='user'
DATABASE_PASSWORD='password'
DATABASE_NAME='db-name'

MEDIA_PATH='var/uploads/media'
 ```