##Requirements

* PHP 7.3
* Sulu 2.0.*
* Symfony 4.3

### Enable the bundle 
 
 Enable the bundle by adding it to the list of registered bundles in the `config/bundles.php` file of your project:
 
 ```php
 return [
     /* ... */
     TheCadien\Bundle\SuluImportExportBundle\SuluImportExportBundle::class => ['all' => true],
 ];
 ```