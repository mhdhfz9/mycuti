<?php

use App\Models\ContactMessage;
use Livewire\Livewire;

test('contact us page is displayed', function () {
    $this->get(route('contact-us'))->assertOk();
});

test('valid submission is stored and shows the success confirmation', function () {
    $response = Livewire::test('pages::⚡contact-us')
        ->set('name', 'Ahmad Bin Ali')
        ->set('phone', '012-3456789')
        ->set('email', 'ahmad@example.com')
        ->set('message', 'Saya ingin bertanya tentang cuti tahunan.')
        ->call('submit');

    $response->assertHasNoErrors();
    $response->assertSet('showSuccess', true);
    $response->assertSet('name', '');

    expect(ContactMessage::count())->toBe(1);

    $message = ContactMessage::first();
    expect($message->name)->toBe('Ahmad Bin Ali');
    expect($message->phone)->toBe('012-3456789');
    expect($message->email)->toBe('ahmad@example.com');
    expect($message->message)->toBe('Saya ingin bertanya tentang cuti tahunan.');
});

test('name is required', function () {
    Livewire::test('pages::⚡contact-us')
        ->set('name', '')
        ->set('phone', '012-3456789')
        ->set('email', 'ahmad@example.com')
        ->set('message', 'Mesej ujian.')
        ->call('submit')
        ->assertHasErrors(['name' => 'required']);

    expect(ContactMessage::count())->toBe(0);
});

test('phone is required', function () {
    Livewire::test('pages::⚡contact-us')
        ->set('name', 'Ahmad')
        ->set('phone', '')
        ->set('email', 'ahmad@example.com')
        ->set('message', 'Mesej ujian.')
        ->call('submit')
        ->assertHasErrors(['phone' => 'required']);
});

test('phone must be a valid malaysian number', function (string $invalidPhone) {
    Livewire::test('pages::⚡contact-us')
        ->set('name', 'Ahmad')
        ->set('phone', $invalidPhone)
        ->set('email', 'ahmad@example.com')
        ->set('message', 'Mesej ujian.')
        ->call('submit')
        ->assertHasErrors(['phone']);

    expect(ContactMessage::count())->toBe(0);
})->with([
    '123',
    '00000000',
    '012345',
    'not-a-phone',
    '+1-555-1234567',
    '021-3456789',
]);

test('phone accepts valid malaysian mobile and landline formats', function (string $validPhone) {
    Livewire::test('pages::⚡contact-us')
        ->set('name', 'Ahmad')
        ->set('phone', $validPhone)
        ->set('email', 'ahmad@example.com')
        ->set('message', 'Mesej ujian.')
        ->call('submit')
        ->assertHasNoErrors();
})->with([
    '012-3456789',
    '0123456789',
    '+60123456789',
    '60123456789',
    '03-12345678',
    '019-8887777',
]);

test('email is required', function () {
    Livewire::test('pages::⚡contact-us')
        ->set('name', 'Ahmad')
        ->set('phone', '012-3456789')
        ->set('email', '')
        ->set('message', 'Mesej ujian.')
        ->call('submit')
        ->assertHasErrors(['email' => 'required']);
});

test('email must be a valid format', function () {
    Livewire::test('pages::⚡contact-us')
        ->set('name', 'Ahmad')
        ->set('phone', '012-3456789')
        ->set('email', 'not-an-email')
        ->set('message', 'Mesej ujian.')
        ->call('submit')
        ->assertHasErrors(['email']);

    expect(ContactMessage::count())->toBe(0);
});

test('message is required', function () {
    Livewire::test('pages::⚡contact-us')
        ->set('name', 'Ahmad')
        ->set('phone', '012-3456789')
        ->set('email', 'ahmad@example.com')
        ->set('message', '')
        ->call('submit')
        ->assertHasErrors(['message' => 'required']);
});

test('message cannot exceed 1000 characters', function () {
    Livewire::test('pages::⚡contact-us')
        ->set('name', 'Ahmad')
        ->set('phone', '012-3456789')
        ->set('email', 'ahmad@example.com')
        ->set('message', str_repeat('a', 1001))
        ->call('submit')
        ->assertHasErrors(['message' => 'max']);

    expect(ContactMessage::count())->toBe(0);
});

test('message at exactly 1000 characters is accepted', function () {
    Livewire::test('pages::⚡contact-us')
        ->set('name', 'Ahmad')
        ->set('phone', '012-3456789')
        ->set('email', 'ahmad@example.com')
        ->set('message', str_repeat('a', 1000))
        ->call('submit')
        ->assertHasNoErrors();

    expect(ContactMessage::count())->toBe(1);
});
