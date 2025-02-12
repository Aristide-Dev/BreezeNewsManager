<?php

namespace App\Models;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    protected $fillable = ['title', 'slug', 'excerpt', 'content', 'image', 'category', 'tags', 'views', 'read_time', 'published_at', 'featured'];

    public function media()
    {
        return $this->morphMany(Media::class, 'mediable');
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tags' => 'array',
            'published_at' => 'date',
            'featured' => 'boolean',
        ];
    }

    public function getTagsAttribute($value)
    {
        return json_decode($value, true);
    }

    public function setTagsAttribute($value)
    {
        $this->attributes['tags'] = json_encode($value);
    }

    // Mutator pour le slug
    public function setTitleAttribute($value)
    {
        $this->attributes['title'] = $value;
        $this->attributes['slug'] = Str::slug($value, '-');
    }

    public static function generateUniqueSlug($title, $id = null)
    {
        $slug = Str::slug($title, '-');
        $originalSlug = $slug;

        $counter = 1;

        while (self::where('slug', $slug)->where('id', '!=', $id)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    public static function calculateReadingTime($content)
    {
        $wordCount = str_word_count(strip_tags($content)); // Compte les mots en supprimant les balises HTML
        $readingSpeed = 200; // Vitesse moyenne en mots par minute

        $readingTime = ceil($wordCount / $readingSpeed); // Arrondir au supérieur
        return $readingTime; // Temps de lecture en minutes
    }


}
