# Laravel Vault

A secure vault implementation for Laravel. It can be used directly, or attached to Laravel models. When attached to models it stores the values of the attributes securely in the Vault table. The attributes in the model's table only contain the corresponding Vault identifiers.

## Installation ##

```
composer require sinevia/laravel-vault
```

## Table Schema ##

The following schema is used for the database.

| Vault    |                  |
|-----------|------------------|
| Id        | String, UniqueId |
| Value     | Long Text        |
| CreatedAt | DateTime         |
| DeletedAt | DateTime         |
| Udated At | DateTime         |

## How to Use ##

### 2. Using Vault Directly ###

```
$vaultId = \Sinevia\Models\Vault::storeValue($value, $password);
```

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

