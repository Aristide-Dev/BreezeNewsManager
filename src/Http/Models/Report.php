<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $fillable = ['title', 'description', 'category', 'tags', 'file_path', 'published_at'];

    const CATEGORIES_LIST = [
        [
            'id'=>'reports',
            'label'=>'Rapports',
        ],
        [
            'id'=>'presentations',
            'label'=>'Présentations',
        ],
        [
            'id'=>'Documents Techniques',
            'label'=>'Documents Techniques',
        ],
        [
            'id'=>'Médias',
            'label'=>'Médias',
        ],
        [
            'id'=>'Ressources',
            'label'=>'Ressources',
        ],
    ];

    public function media()
    {
        return $this->morphMany(Media::class, 'mediable');
    }
}
