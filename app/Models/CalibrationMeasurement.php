<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CalibrationMeasurement extends Model
{
    protected $table = 'calibration_measurements';

    protected $fillable = [
        'calibrationId', 'gageId', 'calibrationDate',
        'standardUsed', 'calibrationStandardGage',
        'measurementBefore', 'measurementAfter',
        'limitMin', 'nominal', 'limitMax', 'uncertainty',
        'units', 'measurementType', 'gageType', 'format', 'comments',
    ];

    protected $casts = [
        'calibrationDate'   => 'date',
        'measurementBefore' => 'float',
        'measurementAfter'  => 'float',
        'limitMin'          => 'float',
        'nominal'           => 'float',
        'limitMax'          => 'float',
        'uncertainty'       => 'float',
    ];

    public function calibration()
    {
        return $this->belongsTo(Calibration::class, 'calibrationId');
    }

    public function gage()
    {
        return $this->belongsTo(Gage::class, 'gageId');
    }
}
