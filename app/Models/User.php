<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\{ HasMany, MorphMany, MorphOne };
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\MediaLibrary\{ HasMedia, InteractsWithMedia };
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class User extends Authenticatable implements HasMedia
{
    use HasApiTokens;
    use HasFactory;
    use InteractsWithMedia;
    use Notifiable;

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * @return HasMany
     */
    public function post() : HasMany
    {
        return $this->hasMany(Post::class);
    }

    /**
     * @param Media|null $media
     *
     * @return void
     */
    public function registerMediaConversions(?Media $media = null) : void
    {
        $this->addMediaConversion('thumb')
            ->width(368)
            ->height(232)
            ->sharpen(10);
    }

    /**
     * Return the profile picture associated with the user.
     *
     * @return MorphOne
     */
    public function profilePicture(): MorphOne
    {
        return $this->morphOne(Media::class, 'model')
            ->where('collection_name', '=', 'avatar');
    }

    /**
     * Return the banner picture associated with the user.
     *
     * @return MorphOne
     */
    public function bannerPicture(): MorphOne
    {
        return $this->morphOne(Media::class, 'model')
            ->where('collection_name', '=', 'banner');
    }

    /**
     * Returns all media associated with the user.
     *
     * @return MorphMany
     */
    public function getAllMedia(): MorphMany
    {
        return $this->morphMany(Media::class, 'model')
            ->whereIn('collection_name', ['avatar', 'banner']);
    }
}
