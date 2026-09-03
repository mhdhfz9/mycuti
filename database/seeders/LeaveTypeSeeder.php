<?php

namespace Database\Seeders;

use App\Models\LeaveType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class LeaveTypeSeeder extends Seeder
{
    /**
     * Seed the standard Malaysian leave types.
     */
    public function run(): void
    {
        $leaveTypes = [
            ['name' => 'Annual Leave', 'default_entitlement_days' => 14, 'is_paid' => true, 'requires_approval' => true, 'colour' => '#2563eb'],
            ['name' => 'Sick Leave', 'default_entitlement_days' => 14, 'is_paid' => true, 'requires_approval' => true, 'colour' => '#dc2626'],
            ['name' => 'Hospitalisation Leave', 'default_entitlement_days' => 60, 'is_paid' => true, 'requires_approval' => true, 'colour' => '#ea580c'],
            ['name' => 'Emergency Leave', 'default_entitlement_days' => 3, 'is_paid' => true, 'requires_approval' => false, 'colour' => '#d97706'],
            ['name' => 'Maternity Leave', 'default_entitlement_days' => 98, 'is_paid' => true, 'requires_approval' => true, 'colour' => '#db2777'],
            ['name' => 'Paternity Leave', 'default_entitlement_days' => 7, 'is_paid' => true, 'requires_approval' => true, 'colour' => '#0891b2'],
            ['name' => 'Marriage Leave', 'default_entitlement_days' => 3, 'is_paid' => true, 'requires_approval' => true, 'colour' => '#9333ea'],
            ['name' => 'Compassionate Leave', 'default_entitlement_days' => 3, 'is_paid' => true, 'requires_approval' => true, 'colour' => '#64748b'],
            ['name' => 'Unpaid Leave', 'default_entitlement_days' => 0, 'is_paid' => false, 'requires_approval' => true, 'colour' => '#475569'],
        ];

        foreach ($leaveTypes as $leaveType) {
            LeaveType::query()->updateOrCreate(
                ['slug' => Str::slug($leaveType['name'])],
                $leaveType,
            );
        }
    }
}
