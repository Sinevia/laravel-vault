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
