<?php

namespace App\VirtualHost;

use App\Models\Host;

class VirtualHostModelParser
{
    /**
     * Virtual Host Model
     *
     * @var Host
     */
    protected $host;

    protected static $modifiers = [];

    public function __construct(Host $host)
    {
        $this->host = $host;
    }

    /**
     * Get Virtual Host Block
     *
     * @return string
     */
    public function getVirtualHostBlock(): string
    {
        $directives = $this->host->directives;

        if ($directives->isEmpty()) {
            return  '';
        }

        $text = '<VirtualHost ' . $directives->get('_addr_port') . '>' . PHP_EOL;

        $entries = $this->getEntries();

        foreach ($entries  as $entry) {
            $text .= "\t {$entry}" . PHP_EOL;
        }

        $text .= "</VirtualHost>";

        return $text;
    }

    /**
     * Get Virtual Host Entries
     *
     * @return array
     */
    public function getEntries(): array
    {
        $directives = $this->host->directives;

        if ($directives->isEmpty()) {
            return  [];
        }

        $entries = [];
        $directives->forget('_addr_port');

        foreach ($directives as $directive => $value) {
            $entries[] = "{$directive} {$value}";
        }

        if (!empty(self::$modifiers)) {
            usort(self::$modifiers, function ($a, $b) {
                return $a["p"] - $b["p"];
            });
            foreach (self::$modifiers as $modifier) {
                $entries = call_user_func($modifier['c'], $this->host, $entries); // $callback();
            }
        }

        return $entries;
    }

    /**
     * Add Virtual Host Entries Modifier
     *
     * @param callable $callback
     * @param integer $priority
     * @return void
     */
    public static function addModifier(callable $callback, int $priority = 1): void
    {
        self::$modifiers[] = ['c' => $callback, 'p' => $priority];
    }
}
