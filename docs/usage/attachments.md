---
title: Attachments
description: Attach, detach, sync, and reorder files on any model
section: usage
order: 2
---

# Attachments

Once a model uses the `HasAttachments` trait, you can manage its files through named collections. A **collection** is just a string label (default: `'default'`) that lets one model hold several independent sets of files — for example a `gallery` and a separate `cover`.

## Attaching a file

`attachFile()` accepts a `File` model or a file id. When `sort` is omitted, the file is appended to the end of the collection:

```php
$product->attachFile($file, collection: 'gallery');
$product->attachFile($fileId, collection: 'gallery');
$product->attachFile($file, collection: 'gallery', sort: 0); // explicit position
```

It returns the created `Attachment` record.

## Detaching a file

```php
// Detach from every collection
$product->detachFile($file);

// Detach only from a specific collection
$product->detachFile($file, 'gallery');
```

`detachFile()` returns the number of attachment rows deleted.

## Reading attachments

```php
// All attachments, ordered by sort
$product->attachments;

// A single collection (returns a MorphMany query)
$product->attachments('gallery')->get();

// A MorphOne for single-file collections
$product->singleAttachment('cover');

// The underlying File models directly
$product->attached_files; // Collection<File>
```

> Eager load files to avoid N+1 queries: `$product->load('attachments.file')`.

## Syncing a collection

`syncAttachments()` replaces the entire collection with the given list, in the given order:

```php
$product->syncAttachments([$id1, $id2, $id3], 'gallery');
```

`syncAttachmentsIfChanged()` does the same but first compares the current ids and order. If they already match, it does nothing and returns `false` — useful in form handlers that re-submit the full list on every save:

```php
$changed = $product->syncAttachmentsIfChanged([$id1, $id2, $id3], 'gallery');

if ($changed) {
    // the collection was actually modified
}
```

Passing `null` or `[]` clears the collection.

## Reordering

Pass a map of file id to its new sort position:

```php
$product->reorderAttachments([
    $fileA->id => 0,
    $fileB->id => 1,
    $fileC->id => 2,
], 'gallery');
```

The underlying `Attachment` model also uses the [`HasSortable`](../advanced/sortable.md) trait, giving each pivot row `moveUp()`, `moveDown()`, `moveToPosition()`, and `swapWith()` helpers when you operate on attachments directly.
