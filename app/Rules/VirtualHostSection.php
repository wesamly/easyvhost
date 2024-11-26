<?php

namespace App\Rules;

use App\Enums\VirtualHostSection as EnumsVirtualHostSection;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class VirtualHostSection implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $keys = array_keys($value);

        $sectionFound = false;
        foreach (EnumsVirtualHostSection::cases() as $section) {
            if (in_array($section->value, $keys)) {
                $sectionFound = true;
                break;
            }
        }

        if (! $sectionFound) {
            // E.g. http, or https
            $supportedSections = '';
            foreach (EnumsVirtualHostSection::cases() as $k => $section) {
                if ($k < count(EnumsVirtualHostSection::cases()) - 1) {
                    $supportedSections .= $section->value.', ';
                } else {
                    $supportedSections .= 'or '.$section->value;
                }
            }

            $fail("The :attribute must have values for {$supportedSections} sections.");
        }
    }
}
