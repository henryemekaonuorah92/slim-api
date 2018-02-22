<?php

namespace App\Base\Helper;

/**
 * Class PaginationHelper
 *
 * @package App\Base\Helper
 */
class PaginationHelper
{
    private $items        = [];
    private $totalItems   = 0;
    private $itemsPerPage = 0;
    private $totalPages   = 0;
    private $currentPage  = 1;
    private $lastPage     = 1;

    /**
     * Paginate
     *
     * @param array $items        : items array
     * @param int   $totalItems   : total items
     * @param int   $itemsPerPage : limit
     * @param int   $currentPage  : current page number
     *
     * @param array $meta
     *
     * @return array
     */
    public function paginate(array $items, int $totalItems, int $itemsPerPage, ?int $currentPage = null, array $meta = []): array
    {
        $this->items        = $items;
        $this->totalItems   = (int)$totalItems;
        $this->itemsPerPage = (int)$itemsPerPage;
        $this->currentPage  = (int)$currentPage;

        $this->lastPage = $this->totalPages = (int)ceil($this->totalItems / $this->itemsPerPage);

        return [
            'total'          => $totalItems,
            'per_page'       => $itemsPerPage,
            'current_page'   => $this->currentPage,
            'last_page'      => $this->lastPage,
            'data'           => $this->items,
            'has_more_pages' => $this->hasMorePages(),
            'has_pages'      => $this->hasPages(),
            'meta'           => $meta,
        ];
    }

    /**
     * Determine if there are more items in the data source.
     *
     * @return bool
     */
    public function hasMorePages(): bool
    {
        return $this->currentPage < $this->lastPage;
    }

    /**
     * Determine if there are enough items to split into multiple pages.
     *
     * @return bool
     */
    public function hasPages(): bool
    {
        return !($this->currentPage == 1 && !$this->hasMorePages());
    }
}