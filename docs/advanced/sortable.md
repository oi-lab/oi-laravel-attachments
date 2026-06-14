---
title: Sorting
description: The HasSortable trait and ordering helpers
section: advanced
order: 3
---

# Sorting

Attachment ordering is backed by a `sort` column on the `attachments` table and the `HasSortable` trait, which the `Attachment` model uses. Most of the time you control ordering through the model API — `attachFile()`, `syncAttachments()`, and `reorderAttachments()` — but the trait's helpers are available when you operate on attachment rows directly.

## Ordering via the model API

```php
// Appends with the next sort value
$product->attachFile($file, 'gallery');

// Sets explicit positions
$product->reorderAttachments([
    $a->id => 0,
    $b->id => 1,
], 'gallery');
```

## HasSortable helpers

When working with an `Attachment` (or any model using `HasSortable`) directly:

```php
$attachment->moveToPosition(3);   // set the sort value
$attachment->moveUp();            // decrement by 1 (clamped at 0)
$attachment->moveUp(2);           // decrement by 2
$attachment->moveDown();          // increment by 1
$attachment->swapWith($other);    // exchange sort values with another row

$attachment->getNextSorted();     // next model by sort order
$attachment->getPreviousSorted(); // previous model by sort order
```

## The sorted scope

```php
Attachment::sorted()->get();        // order by sort asc
Attachment::sorted('desc')->get();  // order by sort desc
```

## Custom sort column

By default the trait uses the `sort` column. To use a different column on your own sortable model, define a `$sortColumn` property:

```php
use OiLab\OiLaravelAttachments\Concerns\HasSortable;

class Page extends Model
{
    use HasSortable;

    protected $sortColumn = 'position';
}
```
