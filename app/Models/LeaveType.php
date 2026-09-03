<?php

namespace App\Models;

use Database\Factories\LeaveTypeFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class LeaveType extends Model
{
    /** @use HasFactory<LeaveTypeFactory> */
    use HasFactory;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'default_entitlement_days',
        'is_paid',
        'requires_approval',
        'colour',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'default_entitlement_days' => 'integer',
            'is_paid' => 'boolean',
            'requires_approval' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (LeaveType $leaveType): void {
            if (blank($leaveType->slug)) {
                $leaveType->slug = Str::slug($leaveType->name);
            }
        });
    }
}
