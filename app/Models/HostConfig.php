<?php

namespace App\Models;

use App\Models\Traits\Paginatable;
use Illuminate\Database\Eloquent\Model;


class HostConfig extends Model
{
	use Paginatable;
	
    protected $fillable = [
        'host_id', 'directive', 'value'
    ];
	
	public $timestamps = false;

	
    
}