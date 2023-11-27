<?php

namespace App\Models;

use App\Models\Traits\Paginatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{
    use Paginatable;

    protected $fillable = [
        'name',
    ];

    public $timestamps = false;

    /**
     * Get Tag Hosts
     */
    public function hosts(): BelongsToMany
    {
        return $this->belongsToMany(Host::class);
    }
}
