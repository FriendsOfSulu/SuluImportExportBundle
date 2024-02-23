## Requirements

* PHP >= 7.3 
* Sulu 2.*
* Symfony 4.4 / 5.* / 6


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

If the doctrine database connection used by Sulu is not the 'default' DBAL
connection you have to specify the connection name in the bundle's configuration.

```yaml
# config/packages/sulu_import_export.yaml

sulu_import_export:
    dbal_connection: default
```

Configure the import and export paths in your `.env` in the following way.

 ```dotenv
MEDIA_PATH='var/uploads/media'

IMPORT_DIR='var/import/'
EXPORT_DIR='var/export/'
 ```

## UPGRADE

### 1.1.1

Since version 1.1.1 it should be possible to change the path of the export and import easily.
For this the ENV must be extended by the following variables.
To map the same function as in the 1.0.0 version it is sufficient to leave the variables empty.

 ```dotenv
IMPORT_DIR=
EXPORT_DIR=
 ```
