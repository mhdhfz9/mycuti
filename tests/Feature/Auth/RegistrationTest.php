<?php

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Laravel\Fortify\Features;

beforeEach(function () {
    $this->skipUnlessFortifyHas(Features::registration());
});

test('registration screen can be rendered', function () {
    $response = $this->get(route('register'));

    $response->assertOk();
});

test('new users can register', function () {
    $response = $this->post(route('register.store'), [
        'name' => 'John Doe',
        'email' => 'test@example.com',
        'ic_number' => '123456789012',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertSessionHasNoErrors()
        ->assertRedirect(route('dashboard', absolute: false));

    $this->assertAuthenticated();
});

test('ic number is required to register', function () {
    $response = $this->post(route('register.store'), [
        'name' => 'John Doe',
        'email' => 'test@example.com',
        'ic_number' => '',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertSessionHasErrors('ic_number');
    $this->assertGuest();
});

test('ic number must be exactly 12 digits', function () {
    $response = $this->post(route('register.store'), [
        'name' => 'John Doe',
        'email' => 'test@example.com',
        'ic_number' => '12345',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertSessionHasErrors('ic_number');
    $this->assertGuest();
});

test('ic number must contain only digits', function () {
    $response = $this->post(route('register.store'), [
        'name' => 'John Doe',
        'email' => 'test@example.com',
        'ic_number' => '12345678901a',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertSessionHasErrors('ic_number');
    $this->assertGuest();
});

test('ic number must be unique', function () {
    User::factory()->create(['ic_number' => '123456789012']);

    $response = $this->post(route('register.store'), [
        'name' => 'Jane Doe',
        'email' => 'jane@example.com',
        'ic_number' => '123456789012',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertSessionHasErrors('ic_number');
    $this->assertGuest();
});

test('ic number is stored encrypted at rest', function () {
    $this->post(route('register.store'), [
        'name' => 'John Doe',
        'email' => 'test@example.com',
        'ic_number' => '123456789012',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $user = User::where('email', 'test@example.com')->first();
    $rawValue = DB::table('users')->where('id', $user->id)->value('ic_number');

    expect($rawValue)->not->toBe('123456789012')
        ->and($user->ic_number)->toBe('123456789012')
        ->and($user->ic_number_hash)->toBe(User::hashIcNumber('123456789012'));
});
