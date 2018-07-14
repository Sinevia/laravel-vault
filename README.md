# Laravel Vault

A secure vault implementation for Laravel.

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

### 1. Vault Key ##
Create a new class app/Helpers/App with method vaultKey. The function should return a string with the key for securing your values. Do not lose your key, or you will not be able to restore your values

### 2. Using Vault Directly ###

```
$vaultId = \Sinevia\Models\Vault::storeValue($value, $password);
```

```
$value = \Sinevia\Model\Vault::retrieveValue($vaultId, $password);
```

### 2. Using Vault with Models ###
