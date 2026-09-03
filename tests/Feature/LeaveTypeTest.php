<?php

use App\Models\LeaveType;
use Database\Seeders\LeaveTypeSeeder;

test('slug is derived from name when not provided', function () {
    $leaveType = LeaveType::factory()->create(['name' => 'Annual Leave', 'slug' => null]);

    expect($leaveType->slug)->toBe('annual-leave');
});

test('boolean and integer fields are cast', function () {
    $leaveType = LeaveType::factory()->create([
        'default_entitlement_days' => '14',
        'is_paid' => 1,
        'requires_approval' => 0,
    ]);

    expect($leaveType->default_entitlement_days)->toBeInt()
        ->and($leaveType->is_paid)->toBeTrue()
        ->and($leaveType->requires_approval)->toBeFalse();
});

test('leave type seeder creates the standard malaysian leave types', function () {
    $this->seed(LeaveTypeSeeder::class);

    expect(LeaveType::query()->count())->toBe(9)
        ->and(LeaveType::query()->where('slug', 'unpaid-leave')->first()->is_paid)->toBeFalse();
});

test('leave type seeder is idempotent', function () {
    $this->seed(LeaveTypeSeeder::class);
    $this->seed(LeaveTypeSeeder::class);

    expect(LeaveType::query()->count())->toBe(9);
});
