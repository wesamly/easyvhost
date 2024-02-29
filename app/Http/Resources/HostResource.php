<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Host
 */
class HostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        return [
            'id' => (int) $this->id,
            'domain' => $this->domain,
            'doc_root_exists' => $this->when($this->configs()->exists(), $this->docRootExists),
            'configs' => HostConfigResource::collection($this->whenLoaded('configs')),
            'tag_ids' => $this->when($this->relationLoaded('tags'), $this->tags->pluck('id')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

        ];
    }
}
