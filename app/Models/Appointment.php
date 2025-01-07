<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'adoption_application_id',
        'appointment_date',
        'appointment_status',
    ];

    public function adoptionApplication()
    {
        return $this->belongsTo(AdoptionApplication::class);
    }

    public static function existsForApplication($applicationId)
    {
        return self::where('adoption_application_id', $applicationId)->exists();
    }
    public function user()
    {
        return $this->adoptionApplication->user();
    }
}