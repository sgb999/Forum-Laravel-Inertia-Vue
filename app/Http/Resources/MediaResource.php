<?php
namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class MediaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $disk  = config('media-library.disk_name');
        $path  = $this->resource->disk ? $this->resource->getPathRelativeToRoot() : $this->resource->fallback_path;
        $thumb = $this->resource->disk ? $this->resource->getPathRelativeToRoot('thumb') : $this->resource->fallback_path;

        $file      = Storage::disk($disk)->get($path);
        $thumbFile = Storage::disk($disk)->get($thumb);
        $mime      = Storage::disk($disk)->mimeType($path);

        return [
            'url'   => 'data:' . $mime . ';base64,' . base64_encode($file),
            'thumb' => 'data:' . $mime . ';base64,' . base64_encode($thumbFile),
        ];
    }
}
