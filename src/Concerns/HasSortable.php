<?php

namespace OiLab\OiLaravelAttachments\Concerns;

use Illuminate\Database\Eloquent\Builder;

/**
 * HasSortable Trait
 *
 * Provides sorting functionality to Eloquent models.
 * Requires a 'sort' column in the database table.
 *
 * @method static Builder sorted(string $direction = 'asc')
 */
trait HasSortable
{
    /**
     * Scope a query to order by the sort column.
     *
     * @param  string  $direction  Sort direction ('asc' or 'desc')
     */
    public function scopeSorted(Builder $query, string $direction = 'asc'): Builder
    {
        return $query->orderBy($this->getSortColumn(), $direction);
    }

    /**
     * Get the name of the "sort" column.
     */
    public function getSortColumn(): string
    {
        return $this->sortColumn ?? 'sort';
    }

    /**
     * Move the model to a specific position.
     *
     * @param  int  $position  The new sort position
     */
    public function moveToPosition(int $position): bool
    {
        return $this->update([$this->getSortColumn() => $position]);
    }

    /**
     * Move the model up in the sort order.
     *
     * @param  int  $positions  Number of positions to move up
     */
    public function moveUp(int $positions = 1): bool
    {
        $newSort = max(0, $this->{$this->getSortColumn()} - $positions);

        return $this->moveToPosition($newSort);
    }

    /**
     * Move the model down in the sort order.
     *
     * @param  int  $positions  Number of positions to move down
     */
    public function moveDown(int $positions = 1): bool
    {
        $newSort = $this->{$this->getSortColumn()} + $positions;

        return $this->moveToPosition($newSort);
    }

    /**
     * Get the next model in the sort order.
     *
     * @return static|null
     */
    public function getNextSorted(): ?self
    {
        return static::query()
            ->where($this->getSortColumn(), '>', $this->{$this->getSortColumn()})
            ->sorted('asc')
            ->first();
    }

    /**
     * Get the previous model in the sort order.
     *
     * @return static|null
     */
    public function getPreviousSorted(): ?self
    {
        return static::query()
            ->where($this->getSortColumn(), '<', $this->{$this->getSortColumn()})
            ->sorted('desc')
            ->first();
    }

    /**
     * Swap positions with another model.
     *
     * @param  self  $other  The model to swap with
     */
    public function swapWith(self $other): bool
    {
        $thisSort = $this->{$this->getSortColumn()};
        $otherSort = $other->{$this->getSortColumn()};

        $this->moveToPosition($otherSort);

        return $other->moveToPosition($thisSort);
    }
}
