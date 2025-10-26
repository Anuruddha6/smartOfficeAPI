<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropertyRooms extends Model
{
    use HasFactory;
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $table = 'property_rooms';

    public function property_room_equipments()
    {
        return $this->hasMany(PropertyRoomEquipments::class, 'property_room_id', 'id');
    }

    public function property_room_features()
    {
        return $this->hasMany(PropertyRoomFeatures::class, 'property_room_id', 'id');
    }
}
