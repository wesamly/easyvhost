<?php

namespace App\Models;

use App\Models\Traits\Paginatable;
use Illuminate\Database\Eloquent\Model;


class Host extends Model
{
	use Paginatable;
	
    protected $fillable = [
        'domain', 'created_at'
    ];
    
    protected $appends = ['docRootExists'];	
	
    public function configs()
    {
        return $this->hasMany(HostConfig::class);
    }

	public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    public function scopeOnlyDirectives($builder, $keys) {
        $builder->whereIn('directive', $keys);
    }

    public function configsDirectives($keys = ['DocumentRoot'])
    {
        return $this->hasMany(HostConfig::class)->whereIn('directive', $keys);
    }
    
    public function getDocRootExistsAttribute()
    {
        
        $config = $this->configs->where('directive', 'DocumentRoot')->first();
        if (!empty($config)) {
            $path = trim($config->value, '"');
            return file_exists($path);
        }
        return false;
    }
}