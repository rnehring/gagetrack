<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Calibration extends Model
{
    protected $table = 'calibrations';
    public $timestamps = false;

    protected $fillable = [
        'gageId', 'dateCalibrated', 'calibrationById', 'calibrationTypeId',
        'calibrationStatusId', 'foundConditionId', 'results', 'actionRequired',
        'findings', 'temperature', 'humidity', 'frequency', 'frequencyUnitId',
        'certificateNumber', 'certificateFilename', 'isPassed', 'timeRequired',
    ];

    protected $casts = [
        'isPassed' => 'boolean',
        'dateCalibrated' => 'date',
    ];

    public function gage()
    {
        return $this->belongsTo(Gage::class, 'gageId');
    }

    public function calibrationBy()
    {
        return $this->belongsTo(Metadata::class, 'calibrationById');
    }

    public function calibrationType()
    {
        return $this->belongsTo(Metadata::class, 'calibrationTypeId');
    }

    public function calibrationStatus()
    {
        return $this->belongsTo(Metadata::class, 'calibrationStatusId');
    }

    public function foundCondition()
    {
        return $this->belongsTo(Metadata::class, 'foundConditionId');
    }

    public function frequencyUnit()
    {
        return $this->belongsTo(Metadata::class, 'frequencyUnitId');
    }
}
