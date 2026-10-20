<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

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

test('login page is accessible', function () {
    $response = $this->get(route('login'));
    $response->assertOk();
});

test('public registration is disabled', function () {
    // Register routes should not exist - accessing them should throw an exception
    try {
        $this->get(route('register'))->assertStatus(404);
    } catch (RouteNotFoundException $e) {
        // Route doesn't exist - this is expected
    }

    try {
        $this->post(route('register.store', []))->assertStatus(404);
    } catch (RouteNotFoundException $e) {
        // Route doesn't exist - this is expected
    }
});
