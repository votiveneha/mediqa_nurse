<?php 

namespace App\Models; 

use Illuminate\Database\Eloquent\Factories\HasFactory; 
use Illuminate\Database\Eloquent\Model; 
use Illuminate\Foundation\Auth\User as Authenticatable; 
use Illuminate\Notifications\Notifiable; 
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable 
{ 
    
    use HasApiTokens, HasFactory, Notifiable; 
    protected $table = 'users'; 
    protected $guarded =[];

    public function getCountryNameAttribute()
    {
        if (!$this->location_country) {
            return null;
        }

        return CountryModel::where('iso2', $this->country)
            ->value('name');
    }
    // public function getStateNameAttribute()
    // {
    //     if (!$this->location_state) {
    //         return null;
    //     }

    //     return StateModel::where('id', $this->location_state)
    //         ->value('name');
    // }
}