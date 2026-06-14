---
title: Uploads
description: Store uploaded files and attach them using the action classes
section: usage
order: 3
---

# Uploads

The package provides two action classes so you never have to assemble a `File` record by hand. They live in `OiLab\OiLaravelAttachments\Actions`.

## StoreUploadedFile

Persists a single `UploadedFile` to the configured disk and returns the created `File` model. It generates a UUID filename, stores the original download name, captures the MIME type, file size, MD5 hash, and — for images — the width and height:

```php
use OiLab\OiLaravelAttachments\Actions\StoreUploadedFile;

$file = StoreUploadedFile::handle($request->file('document'));

// Override the disk and directory if needed
$file = StoreUploadedFile::handle(
    $request->file('document'),
    disk: 's3',
    directory: 'documents',
);
```

## AttachUploadedFiles

Stores an array of uploads and attaches each resulting `File` to a model in one call. It uses the disk and directory from `config/oi-laravel-attachments.php`:

```php
use OiLab\OiLaravelAttachments\Actions\AttachUploadedFiles;

AttachUploadedFiles::handle($product, $request->file('images'), 'gallery');
```

Passing an empty array is a no-op.

## In a controller

```php
use Illuminate\Http\Request;
use OiLab\OiLaravelAttachments\Actions\AttachUploadedFiles;
use OiLab\OiLaravelAttachments\Actions\StoreUploadedFile;

public function store(Request $request, Product $product)
{
    $request->validate([
        'cover' => ['required', 'image'],
        'images' => ['array'],
        'images.*' => ['image'],
    ]);

    // Single file → store, then attach to a single-file collection
    $cover = StoreUploadedFile::handle($request->file('cover'));
    $product->syncAttachments([$cover->id], 'cover');

    // Many files → store and attach in one call
    AttachUploadedFiles::handle($product, $request->file('images') ?? [], 'gallery');

    return back();
}
```

## Storage location

Files are written to `{disk}/{directory}/{uuid}.{ext}` and the relative path is stored in the File's `filename_disk` column. To read the file back, use the [`File`](files-and-folders.md) model's `getFullPath()` or `getStream()` helpers rather than reconstructing the path.
