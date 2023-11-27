<?php

namespace App\Http\Controllers;

use App\Http\Requests\TagEditRequest;
use App\Http\Requests\TagListRequest;
use App\Http\Resources\TagResource;
use App\Models\Tag;

class TagController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(TagListRequest $request)
    {
        $query = new Tag;
        if ($request->has('include') && $request->include == 'hosts_count') {
            $query = $query->withCount('hosts');
        }
        $records = $query->orderBy('name')->get();

        return TagResource::collection($records);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TagEditRequest $request)
    {
        $tag = Tag::create($request->validated());

        return new TagResource($tag);

    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $tag = Tag::findOrFail($id);

        return new TagResource($tag);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TagEditRequest $request, int $id)
    {
        $tag = Tag::findOrFail($id);
        $tag->update($request->validated());
        $tag->save();

        return new TagResource($tag);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        $tag = Tag::findOrFail($id);
        $tag->delete();

        return response()->noContent();
    }
}
