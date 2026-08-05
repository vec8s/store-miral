<?php

declare(strict_types=1);

namespace App\Domains\Media\Models;

use App\Domains\Media\Enums\MediaCollection;
use App\Domains\Media\Enums\MediaDisk;
use App\Shared\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Media extends BaseModel
{
    use HasFactory;

    protected $fillable = ['mediable_type', 'mediable_id', 'collection', 'disk', 'path', 'filename', 'mime_type', 'size', 'width', 'height', 'responsive_images', 'alt_text', 'sort_order', 'uploaded_by_id'];
    protected function casts(): array { return ['collection' => MediaCollection::class, 'disk' => MediaDisk::class, 'size' => 'integer', 'width' => 'integer', 'height' => 'integer', 'responsive_images' => 'array', 'sort_order' => 'integer']; }
    public function mediable(): MorphTo { return $this->morphTo(); }
    public function scopeInCollection($q, MediaCollection $c) { return $q->where('collection', $c); }
}
