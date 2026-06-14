---
title: Installation
description: How to install OI Laravel Attachments via Composer
section: getting-started
order: 2
---

# Installation

## Via Composer

```bash
composer require oi-lab/oi-laravel-attachments
```

The package auto-discovers and registers its service provider via Laravel's package discovery — no manual registration required.

## Publish the migrations

The package ships the `folders`, `files`, and `attachments` migrations. Publish them into your application and run them:

```bash
php artisan vendor:publish --tag=oi-laravel-attachments-migrations
php artisan migrate
```

> The migrations are also loaded automatically from the package, so `php artisan migrate` works even without publishing. Publish them only when you need to customize the schema.

## Publish the configuration (optional)

```bash
php artisan vendor:publish --tag=oi-laravel-attachments-config
```

This creates `config/oi-laravel-attachments.php` with sensible defaults. See [Configuration](../configuration/configuration.md) for all available options.

## Local development

To use the package from a local checkout alongside your project, add a `path` repository to your project's `composer.json`:

```json
{
    "repositories": [
        {
            "type": "path",
            "url": "./packages/oi-lab/oi-laravel-attachments"
        }
    ]
}
```

Then require it:

```bash
composer require oi-lab/oi-laravel-attachments
```

## Verify the installation

Add the `HasAttachments` trait to a model and attach a file to confirm the wiring:

```php
use OiLab\OiLaravelAttachments\Concerns\HasAttachments;
use OiLab\OiLaravelAttachments\Models\File;

class Product extends Model
{
    use HasAttachments;
}

$product->attachFile(File::first());
$product->attachments; // should contain one attachment
```
