<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

test('user can register with valid data and is automatically logged in', function () {
    $response = $this->post(route('register.store'), [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertRedirect(route('dashboard'));
    $this->assertAuthenticated();
    $this->assertDatabaseHas('users', [
        'email' => 'john@example.com',
    ]);

    $user = User::where('email', 'john@example.com')->first();
    expect(Hash::check('password123', $user->password))->toBeTrue();
    expect($user->password)->not->toBe('password123');
});

test('registration is rejected if email already exists', function () {
    User::factory()->create(['email' => 'existing@example.com']);

    $response = $this->post(route('register.store'), [
        'name' => 'John Doe',
        'email' => 'existing@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertSessionHasErrors('email');
    $this->assertGuest();
});

test('registration is rejected if password does not match confirmation', function () {
    $response = $this->post(route('register.store'), [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => 'password123',
        'password_confirmation' => 'wrong-confirmation',
    ]);

    $response->assertSessionHasErrors('password');
    $this->assertGuest();
});

test('registration is rejected if password is shorter than 8 characters', function () {
    $response = $this->post(route('register.store'), [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => 'short',
        'password_confirmation' => 'short',
    ]);

    $response->assertSessionHasErrors('password');
    $this->assertGuest();
});

test('registration is rejected if name is missing', function () {
    $response = $this->post(route('register.store'), [
        'email' => 'john@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertSessionHasErrors('name');
    $this->assertGuest();
});

test('user can login with correct credentials', function () {
    $user = User::factory()->create([
        'email' => 'john@example.com',
        'password' => Hash::make('password123'),
    ]);

    $response = $this->post(route('login.store'), [
        'email' => 'john@example.com',
        'password' => 'password123',
    ]);

    $response->assertRedirect(route('dashboard'));
    $this->assertAuthenticatedAs($user);
});

test('login is rejected with wrong credentials', function () {
    User::factory()->create([
        'email' => 'john@example.com',
        'password' => Hash::make('password123'),
    ]);

    $response = $this->post(route('login.store'), [
        'email' => 'john@example.com',
        'password' => 'wrong-password',
    ]);

    $response->assertRedirect(route('login'));
    $response->assertSessionHasErrors('email');
    $this->assertGuest();
});

test('login is rejected with non-existent email', function () {
    $response = $this->post(route('login.store'), [
        'email' => 'unknown@example.com',
        'password' => 'password123',
    ]);

    $response->assertRedirect(route('login'));
    $response->assertSessionHasErrors('email');
    $this->assertGuest();
});

test('user can logout and cannot access protected routes afterwards', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $response = $this->post(route('logout'));
    $response->assertRedirect(route('login'));

    $this->assertGuest();

    $dashboard = $this->get(route('dashboard'));
    $dashboard->assertRedirect(route('login'));
});

test('unauthenticated user cannot access protected dashboard route', function () {
    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('login'));
});

test('authenticated user can access dashboard', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('dashboard'));
    $response->assertOk();
});

test('registration page is accessible', function () {
    $response = $this->get(route('register'));
    $response->assertOk();
});

test('login page is accessible', function () {
    $response = $this->get(route('login'));
    $response->assertOk();
});
