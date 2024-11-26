<?php

namespace App\Models;

use App\Models\Traits\Paginatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class Host extends Model
{
    use HasFactory, Paginatable;

    protected $fillable = [
        'domain', 'created_at',
    ];

    protected $appends = ['docRootExists', 'sslEnabled'];

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
        /** @var ?HostConfig $config */
        $config = $this->configs->where('directive', 'DocumentRoot')->first();
        if (! empty($config)) {
            $path = trim($config->value, '"');

            return file_exists($path);
        }

        return false;
    }

    public function getDirectives(string $section): Collection
    {
        return $this->configs->where('section', $section)->pluck('value', 'directive');
    }

    public function getSslEnabledAttribute(): bool
    {
        $entry = $this->configs
            ->where('section', 'https')
            ->where('directive', 'SSLEngine')
            ->where('value', 'On')
            ->first();

        return ! empty($entry);
    }
}
