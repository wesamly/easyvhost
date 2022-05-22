<?php

namespace App\Http\Controllers;

use App\Http\Requests\TagEditRequest;
use App\Http\Requests\TagListRequest;
use App\Http\Resources\TagResource;
use App\Models\Tag;
use Illuminate\Http\Response;

class TagController extends Controller
{
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
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
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(TagEditRequest $request)
    {
        $tag = Tag::create($request->validated());
        return new TagResource($tag);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $tag = Tag::findOrFail($id);
        return new TagResource($tag);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(TagEditRequest $request, $id)
    {
        $tag = Tag::findOrFail($id);
        $tag->update($request->validated());
        $tag->save();
        return new TagResource($tag);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $tag = Tag::findOrFail($id);
        $tag->delete();
        return response()->noContent();
    }
}
