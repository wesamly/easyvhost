<?php

namespace App\Http\Controllers;

use App\Events\SettingsUpdated;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    /**
     * Return Settings.
     */
    public function index()
    {
        $rawSettings = setting()->all();

        $files = [];
        if (isset($rawSettings['files']) && ! empty($rawSettings['files'])) {
            $files = json_decode($rawSettings['files'], true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $files = [];
            }
        }

        $settings = [
            'configs' => [
                'default' => [
                    'file' => $rawSettings['default_file'] ?? '',
                    'tags' => [],
                ],
                'files' => $files,
            ],

        ];

        return response()->json(['data' => $settings]);
    }

    /**
     * Save Settings.
     */
    public function save(Request $request)
    {

        // Default config file
        if ($request->has('configs.default')) {
            setting()->set('default_file', $request->input('configs.default.file'));
        }
        // Per tag config files
        if ($request->has('configs.files')) {
            setting()->set('files', json_encode($request->input('configs.files')));
        }

        setting()->save();

        SettingsUpdated::dispatch();

        return response()->noContent();

    }
}
