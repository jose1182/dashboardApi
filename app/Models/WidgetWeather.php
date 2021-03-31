<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class WidgetWeather extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'api_url',
        'user_id',
    ];

    public function user(){
        return $this->belongsTo(User::Class);
    }
}
