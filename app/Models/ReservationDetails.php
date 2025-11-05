<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class ReservationDetails extends Model
{
    use HasFactory;
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $table = 'reservation_details';

    public function reservation_detail_equipments()
    {
        return $this->hasMany(ReservationDetailEquipments::class, 'reservation_detail_id', 'id');
    }

    public function reservation_detail_features()
    {
        return $this->hasMany(ReservationDetailFeatures::class, 'reservation_detail_id', 'id');
    }
}
