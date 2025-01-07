<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Listing extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'desc',
        'species',
        'breed',
        'color',
        'image',
        'status', // Add status to fillable attributes
        'application_id',

    
    ];

    public function adoptionApplication()
    {
        return $this->hasOne(AdoptionApplication::class, 'listing_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function adoptionApplications()
    {
        return $this->hasMany(AdoptionApplication::class);
    }
    
    public function scopeFilter($query, array $filters)
    {
        if ($filters['search'] ?? false) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('desc', 'like', '%' . $filters['search'] . '%');
            });
        }

        if ($filters['user_id'] ?? false) {
            $query->where('user_id', $filters['user_id']);
        }

        if ($filters['tag'] ?? false) {
            $query->whereHas('tags', function ($query) use ($filters) {
                $query->where('name', $filters['tag']);
            });
        }
    }
}