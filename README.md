# Laravel Vault

Vault - a secure value storage (data-at-rest) implementation for Laravel. It can be used directly, or attached to Laravel models. When attached to models it stores the values of the attributes securely in the Vault's table. The attributes in the model's table only contain the corresponding identifiers referencing the Vault's table.

## Installation ##

```
composer require sinevia/laravel-vault
```

## Table Schema ##

The following schema is used for the database.

| Vault     |                  |
|-----------|------------------|
| Id        | String, UniqueId |
| Value     | Long Text        |
| CreatedAt | DateTime         |
| DeletedAt | DateTime         |
| UpdatedAt | DateTime         |

## How to Use ##

### 1. Migrations ###

- 1.1. Add the migration file to your database/migrations directory

```
<?php

class VaultTablesCreate extends Illuminate\Database\Migrations\Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Sinevia\Vault\Models\Vault::tableCreate();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {        
        Sinevia\Vault\Models\Vault::tableDelete();
    }

}
```

- 1.2. Run the migrations to create the Vault's table

```
php artisan migrate
```


### 2. Using Vault Directly ###

- 2.1 Store a value to the vault. If successful, will return the Vault id, that the value was securely stored under

```
$vaultId = \Sinevia\Models\Vault::storeValue($value, $password);
```

- 2.2 Retrieving the value using the Vault id it was stored under, (see the example above).

```
$value = \Sinevia\Model\Vault::retrieveValue($vaultId, $password);
```

### 3. Using Vault with Models ###

- 3.1. Vault Key

Create a new class app/Helpers/App with method vaultKey. The function should return a string with the key for securing your values. Do not lose your key, or you will not be able to restore your values

- 3.2. Add the VaultAttribute trait to the model

```
use \Sinevia\Vault\VaultAttributeTrait;
```

- 3.3. Specify the attributes to be used with Vault

```
class ExampleModel {

    use \Sinevia\Vault\VaultAttributeTrait;
    
    public function getUsernameAttribute($value) {
        return $this->getVaultAttribute($value);
    }
    
    public function setUsernameAttribute($value) {
        return $this->setVaultAttribute('Username', $value);
    }
}
```

- 3.4. Use the vaulted attributes

```
$exampleModel->Username = 'test';
$exampleModel->save();
```

