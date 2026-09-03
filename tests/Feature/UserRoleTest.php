<?php

use App\Enums\UserRole;
use App\Models\User;

test('users default to the employee role with no manager', function () {
    $user = User::factory()->create();

    expect($user->role)->toBe(UserRole::Employee)
        ->and($user->manager_id)->toBeNull();
});

test('the manager and admin factory states set the role', function () {
    $manager = User::factory()->manager()->create();
    $admin = User::factory()->admin()->create();

    expect($manager->role)->toBe(UserRole::Manager)
        ->and($admin->role)->toBe(UserRole::Admin);
});

test('a user can be assigned a manager', function () {
    $manager = User::factory()->manager()->create();
    $employee = User::factory()->managedBy($manager)->create();

    expect($employee->manager_id)->toBe($manager->id)
        ->and($employee->manager->is($manager))->toBeTrue();
});

test('a manager can see their direct reports', function () {
    $manager = User::factory()->manager()->create();
    $reports = User::factory()->count(2)->managedBy($manager)->create();

    expect($manager->directReports)->toHaveCount(2)
        ->and($manager->directReports->pluck('id')->sort()->values()->all())
        ->toBe($reports->pluck('id')->sort()->values()->all());
});
