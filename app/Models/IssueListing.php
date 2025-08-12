<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class IssueListing extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

    public $timestamps = false;
    protected $fillable = ['done', 'email_fixed', 'date_fixed', 'comment_fixed', 'priority'];

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')
              ->width(360)
              ->height(360)
              ->sharpen(10);
    }

    /**
     * The listissues that belong to the Roomlist.
     */
    public function issue()
    {
        return $this->belongsTo(Issue::class);
    }

    /**
     * Get the user that requested the listissues.
     */
    public function userRequested()
    {
        return $this->belongsTo(User::class, 'user_requested');
    }

    /**
     * Get the user that owns the listissues.
     */
    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function scopeActive($query)
    {
        $query->where('done', 0);
    }

    public function scopeFixed($query)
    {
        $query->where('done', 1);
    }
}
