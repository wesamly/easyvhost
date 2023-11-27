<?php

namespace App\Models;

use App\Models\Traits\Paginatable;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use Paginatable;

    protected $fillable = [
        'name',
    ];

    public $timestamps = false;

    /**
     * Get Tag Hosts
     *
     * @return object[]
     */
    public function hosts()
    {
        return $this->belongsToMany(Host::class);
    }
}
