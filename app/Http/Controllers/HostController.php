<?php

namespace App\Http\Controllers;

use App\Events\HostCreated;
use App\Events\HostDeleted;
use App\Events\HostUpdated;
use App\Http\Requests\HostEditRequest;
use App\Http\Requests\HostListRequest;
use App\Http\Resources\HostResource;
use App\Models\Host;
use App\Models\HostConfig;
use Illuminate\Http\Response;

class HostController extends Controller
{
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(HostListRequest $request)
    {

        $query = new Host;
        if ($request->filled('configs')) {
            $configs = $request->configs;
            $query = $query->with(['configs' => function($q) use ($configs) {
                $q->whereIn('directive', array_map('trim', explode(',', $configs)));
            }]);
            
        }
        $records = $query->latest()->paginate();
        return HostResource::collection($records);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
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

        return new HostResource($host);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $host = Host::with(['configs', 'tags'])->findOrFail($id);
        //$host->load(['configs', 'tags']);
        return new HostResource($host);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(HostEditRequest $request, $id)
    {
        $host = Host::findOrFail($id);
        $host->update($request->validated());
        $host->save();
        
        // Update Directives
        $currentDirectives = $host->configs->pluck('directive')->toArray();
        $requestDirectives = array_keys($request->config);

        foreach ($request->config as $directive => $value) {
            $config = $host->configs()->where('directive', $directive)->first();
            if (!is_null($config)) {
                $config->value = $value;
                $config->save();
            } else {
                $host->configs()->save(new HostConfig(['directive' => $directive, 'value' => $value]));
            }
        }

        $deletedDirectives = array_diff($currentDirectives, $requestDirectives);
        if (!empty($deletedDirectives)) {
            $host->configs()->whereIn('directive', $deletedDirectives)->delete();
        }

        // Update Tags
        $tagIds = $request->has('tags') ? $request->tags : [];
        $host->tags()->sync($tagIds);

        $host->touch(); // Force updating updated_at
        $host->fresh();

        HostUpdated::dispatch($host);

        return new HostResource($host);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $host = Host::findOrFail($id);
        $host->delete();

        HostDeleted::dispatch($host);
        
        return response()->noContent();
    }
}
