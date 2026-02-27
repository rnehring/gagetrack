<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gage extends Model
{
    protected $table = 'gages';
    public $timestamps = false;

    protected $fillable = [
        'gageNumber', 'description', 'serialNumber', 'modelNumber', 'nistNumber',
        'notes', 'statusId', 'typeId', 'locationId', 'manufacturerId', 'ownerId',
        'unitMeasureId', 'frequencyUnitId', 'supplierId', 'frequency', 'dateDue', 'isActive',
    ];

    protected $casts = [
        'dateDue' => 'date',
        'isActive' => 'boolean',
    ];

    public function status()
    {
        return $this->belongsTo(Metadata::class, 'statusId');
    }

    public function type()
    {
        return $this->belongsTo(Metadata::class, 'typeId');
    }

    public function location()
    {
        return $this->belongsTo(Metadata::class, 'locationId');
    }

    public function manufacturer()
    {
        return $this->belongsTo(Metadata::class, 'manufacturerId');
    }

    public function owner()
    {
        return $this->belongsTo(Metadata::class, 'ownerId');
    }

    public function unitMeasure()
    {
        return $this->belongsTo(Metadata::class, 'unitMeasureId');
    }

    public function frequencyUnit()
    {
        return $this->belongsTo(Metadata::class, 'frequencyUnitId');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplierId');
    }

    public function calibrations()
    {
        return $this->hasMany(Calibration::class, 'gageId');
    }

    public function latestPassedCalibration()
    {
        return $this->hasOne(Calibration::class, 'gageId')
            ->where('isPassed', 1)
            ->orderByDesc('dateCalibrated');
    }

    /**
     * Calculate new due date based on frequency.
     */
    public function calculateDueDate(string $baseDate, int $frequency, int $frequencyUnitId): string
    {
        $unit = match ($frequencyUnitId) {
            716 => 'days',
            718 => 'months',
            720 => 'years',
            default => null,
        };

        if (! $unit) {
            return '0000-00-00';
        }

        return date('Y-m-d', strtotime("+{$frequency} {$unit}", strtotime($baseDate)));
    }

    /**
     * Determine if gage is overdue.
     */
    public function getIsOverdueAttribute(): bool
    {
        if (! $this->dateDue || $this->dateDue->year < 2000) {
            return false;
        }

        return $this->dateDue->isPast();
    }

    /**
     * Get frequency display string.
     */
    public function getFrequencyDisplayAttribute(): string
    {
        if (! $this->frequency) {
            return '';
        }
        $unit = $this->frequencyUnit ? $this->frequencyUnit->value : '';
        $plural = ($this->frequency > 1) ? 's' : '';
        return "Every {$this->frequency} {$unit}{$plural}";
    }
}
