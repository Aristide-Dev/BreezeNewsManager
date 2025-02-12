<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    protected $fillable = ['type', 'title', 'url', 'description', 'category', 'embed_url', 'duration', 'published_at'];

    
    public function mediable()
    {
        return $this->morphTo();
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'published_at' => 'date',
        ];
    }
}
