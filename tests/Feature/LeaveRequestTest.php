<?php

use App\Enums\LeaveRequestStatus;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\User;
use Carbon\CarbonInterface;

test('leave request belongs to a user and a leave type', function () {
    $user = User::factory()->create();
    $leaveType = LeaveType::factory()->create();

    $leaveRequest = LeaveRequest::factory()->create([
        'user_id' => $user->id,
        'leave_type_id' => $leaveType->id,
    ]);

    expect($leaveRequest->user->is($user))->toBeTrue()
        ->and($leaveRequest->leaveType->is($leaveType))->toBeTrue();
});

test('status defaults to pending and is cast to the enum', function () {
    $leaveRequest = LeaveRequest::factory()->create();

    expect($leaveRequest->status)->toBe(LeaveRequestStatus::Pending);
});

test('dates are cast to Carbon instances', function () {
    $leaveRequest = LeaveRequest::factory()->create([
        'start_date' => '2026-10-01',
        'end_date' => '2026-10-03',
    ]);

    expect($leaveRequest->start_date)->toBeInstanceOf(CarbonInterface::class)
        ->and($leaveRequest->end_date)->toBeInstanceOf(CarbonInterface::class);
});

test('approved factory state sets an approver and approved_at', function () {
    $leaveRequest = LeaveRequest::factory()->approved()->create();

    expect($leaveRequest->status)->toBe(LeaveRequestStatus::Approved)
        ->and($leaveRequest->approver)->not->toBeNull()
        ->and($leaveRequest->approved_at)->not->toBeNull();
});

test('a user can access their submitted leave requests', function () {
    $user = User::factory()->create();
    LeaveRequest::factory()->count(3)->create(['user_id' => $user->id]);

    expect($user->leaveRequests()->count())->toBe(3);
});
