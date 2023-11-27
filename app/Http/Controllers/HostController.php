<?php

namespace App\Http\Controllers;

use App\Events\HostCreated;
use App\Events\HostDeleted;
use App\Events\HostUpdated;
use App\Http\Requests\HostEditRequest;
use App\Http\Requests\HostListRequest;
use App\Http\Resources\HostDetailsResource;
use App\Http\Resources\HostResource;
use App\Models\Host;
use App\Models\HostConfig;

class HostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(HostListRequest $request)
    {

        $query = new Host;
        if ($request->filled('configs')) {
            $configs = $request->configs;
            $query = $query->with(['configs' => function ($q) use ($configs) {
                $q->whereIn('directive', array_map('trim', explode(',', $configs)));
            }]);

        }
        if ($request->filled('tags')) {
            $query = $query->with(['tags']);
        }

        $records = $query->latest()->paginate();

        return HostResource::collection($records);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(HostEditRequest $request)
    {
        $host = Host::create($request->validated());

        foreach ($request->config as $directive => $value) {
            $host->configs()->save(new HostConfig(['directive' => $directive, 'value' => $value]));
        }

        $tagIds = $request->has('tags') ? $request->tags : [];
        $host->tags()->sync($tagIds);

        HostCreated::dispatch($host);

        return new HostDetailsResource($host);

    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $host = Host::with(['configs', 'tags'])->findOrFail($id);

        return new HostDetailsResource($host);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(HostEditRequest $request, int $id)
    {
        $host = Host::findOrFail($id);
        $host->update($request->validated());
        $host->save();

        // Update Directives
        $currentDirectives = $host->configs->pluck('directive')->toArray();
        $requestDirectives = array_keys($request->config);

        foreach ($request->config as $directive => $value) {
            $config = $host->configs()->where('directive', $directive)->first();
            if (! is_null($config)) {
                $config->value = $value;
                $config->save();
            } else {
                $host->configs()->save(new HostConfig(['directive' => $directive, 'value' => $value]));
            }
        }

        $deletedDirectives = array_diff($currentDirectives, $requestDirectives);
        if (! empty($deletedDirectives)) {
            $host->configs()->whereIn('directive', $deletedDirectives)->delete();
        }

        // Update Tags
        $tagIds = $request->has('tags') ? $request->tags : [];
        $host->tags()->sync($tagIds);

        $host->touch(); // Force updating updated_at
        $host->fresh();

        HostUpdated::dispatch($host);

        return new HostDetailsResource($host);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        $host = Host::findOrFail($id);
        $host->delete();

        HostDeleted::dispatch($host);

        return response()->noContent();
    }
}
