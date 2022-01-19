<?php

namespace App\Models;

use App\Models\Traits\Paginatable;
use Illuminate\Database\Eloquent\Model;


class Host extends Model
{
	use Paginatable;
	
    protected $fillable = [
        'domain'
    ];
	
	public function configs()
    {
        return $this->hasMany(HostConfig::class);
    }

	public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }
    
}