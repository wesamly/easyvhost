<?php

namespace App\Models;

use App\Models\Traits\Paginatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class Host extends Model
{
    use Paginatable;

    protected $fillable = [
        'domain', 'created_at',
    ];

    protected $appends = ['docRootExists'];

    /**
     * Get Host Configs
     */
    public function configs(): HasMany
    {
        return $this->hasMany(HostConfig::class);
    }

    /**
     * Get Host Tags
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    /**
     * Check DocumentRoot path exists
     */
    public function getDocRootExistsAttribute(): bool
    {

        $config = $this->configs->where('directive', 'DocumentRoot')->first();
        if (! empty($config)) {
            $path = trim($config->value, '"');

            return file_exists($path);
        }

        return false;
    }

    /**
     * Host Directives
     */
    public function getDirectivesAttribute(): Collection
    {
        return $this->configs->pluck('value', 'directive');
    }

    /**
     * Get the VirtualHost Block
     */
    public function virtualHostBlock(): string
    {
        $directives = $this->directives;

        if ($directives->isEmpty()) {
            return '';
        }

        $text = '<VirtualHost '.$directives->get('_addr_port').'>'.PHP_EOL;
        $directives->forget('_addr_port');

        foreach ($directives as $directive => $value) {
            $text .= "\t {$directive} {$value}".PHP_EOL;
        }

        $text .= '</VirtualHost>';

        return $text;
    }
}
