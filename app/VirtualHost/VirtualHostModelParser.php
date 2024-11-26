<?php

namespace App\VirtualHost;

use App\Enums\VirtualHostSection;
use App\Models\Host;

class VirtualHostModelParser
{
    protected static $modifiers = [];

    public function __construct(private Host $host)
    {
        $this->host = $host;
    }

    /**
     * Get Virtual Host Blocks including http and https blocks (if defined)
     */
    public function getVirtualHostBlocks(): string
    {
        $text = '';
        foreach (VirtualHostSection::cases() as $section) {
            $text .= $this->getVirtualHostBlock($section->value);
        }

        return $text;
    }

    public function getVirtualHostBlock(string $section): string
    {
        $directives = $this->host->getDirectives($section);
        if ($directives->isEmpty()) {
            return '';
        }

        $addrPort = $directives->get('_addr_port');
        if (empty($addrPort)) {
            $addrPort = config('vhosts.addr_ports.'.$section);
        }

        $entries = $this->getEntries($section);
        if (empty($entries)) {
            return '';
        }

        $text = '<VirtualHost '.$addrPort.'>'.PHP_EOL;

        foreach ($entries as $entry) {
            $text .= "\t {$entry}".PHP_EOL;
        }

        $text .= '</VirtualHost>'.PHP_EOL;

        return $text;
    }

    /**
     * Get Virtual Host Entries for a given section
     */
    public function getEntries(string $section): array
    {
        $directives = $this->host->getDirectives($section);

        if ($directives->isEmpty()) {
            return [];
        }

        if ($section == VirtualHostSection::HTTPS->value) {
            if ($directives->get('SSLEngine') != 'On') {
                return [];
            }

            $shareHttpDirectives = $directives->get('_share_directives') ?? false;
            if ($shareHttpDirectives) {
                $httpDirectives = $this->host->getDirectives(VirtualHostSection::HTTP->value);
                $directives = $directives->merge($httpDirectives);
            }
        }

        $entries = [];
        foreach ($directives as $directive => $value) {
            if (substr($directive, 0, 1) == '_') {
                $directives->forget($directive);
            }
        }

        foreach ($directives as $directive => $value) {
            if (empty($value)) {
                continue;
            }
            $entries[] = "{$directive} {$value}";
        }

        if (! empty(self::$modifiers)) {
            usort(self::$modifiers, function ($a, $b) {
                return $a['p'] - $b['p'];
            });
            foreach (self::$modifiers as $modifier) {
                $entries = call_user_func($modifier['c'], $this->host, $entries, $section);
            }
        }

        return $entries;
    }

    /**
     * Add Virtual Host Entries Modifier
     */
    public static function addModifier(callable $callback, int $priority = 1): void
    {
        self::$modifiers[] = ['c' => $callback, 'p' => $priority];
    }
}
