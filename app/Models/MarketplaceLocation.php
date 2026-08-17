<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class MarketplaceLocation extends Model
{
    protected $fillable=['name','slug','type','district','state','pincode','latitude','longitude','is_active'];
    protected $casts=['latitude'=>'float','longitude'=>'float','is_active'=>'boolean'];
    public function businesses(){return $this->hasMany(Business::class,'location_id');}
}
