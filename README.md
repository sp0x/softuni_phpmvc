xCart
========================

Init bundles

 composer require symfony-cmf/routing-auto-bundle \
    symfony-cmf/menu-bundle \
    sonata-project/doctrine-phpcr-admin-bundle \
    symfony-cmf/tree-browser-bundle \
    doctrine/data-fixtures \
    symfony-cmf/routing-bundle
    
Create database  
symfony console doctrine:database:create


Install doctrine  
```shell script
composer require symfony/orm-pack
```

Migrate the DB Structure
```shell script
php bin/console doctrine:migrations:migrate
```
 
Login details for admin are:
admin
alongpass
 