<?php

namespace App\Models;

use App\Models\Traits\Paginatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Host extends Model
{
	use Paginatable;
	
    protected $fillable = [
        'domain', 'created_at'
    ];
    
    protected $appends = ['docRootExists'];	
	
    /**
     * Get Host Configs
     *
     * @return object[]
     */
    public function configs()
    {
        return $this->hasMany(HostConfig::class);
    }

    /**
     * Get Host Tags
     *
     * @return object[]
     */
	public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }
    
    /**
     * Check DocumentRoot path exists
     *
     * @return boolean
     */
    public function getDocRootExistsAttribute() : bool
    {
        
        $config = $this->configs->where('directive', 'DocumentRoot')->first();
        if (!empty($config)) {
            $path = trim($config->value, '"');
            return file_exists($path);
        }
        return false;
    }

    /**
     * Host Directives
     *
     * @return Collection
     */
    public function getDirectivesAttribute() : Collection
    {
        return $this->configs->pluck('value', 'directive');
    }

    /**
     * Get the VirtualHost Block
     *
     * @return string
     */
    public function virtualHostBlock() : string
    {
        $directives = $this->directives;

        if ($directives->isEmpty()) {
            return  '';
        }

        $text = '<VirtualHost ' . $directives->get('_addr_port') . '>' . PHP_EOL;
        $directives->forget('_addr_port');

        foreach ($directives as $directive => $value) {
            $text .= "\t {$directive} {$value}" . PHP_EOL;
        }

        $text .= "</VirtualHost>";

        return $text;
    }    
}