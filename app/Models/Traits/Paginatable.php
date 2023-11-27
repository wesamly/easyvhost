<?php

namespace App\Models\Traits;

trait Paginatable
{
    protected $perPageMax = 100000;

    /**
     * Get the number of models to return per page.
     */
    public function getPerPage(): int
    {
        $perPage = request('per_page', $this->perPage);

        if ($perPage === 'all') {
            $perPage = $this->count();
        }

        return max(1, min($this->perPageMax, (int) $perPage));
    }

    public function setPerPageMax(int $perPageMax): void
    {
        $this->perPageMax = $perPageMax;
    }
}
