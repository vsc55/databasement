<?php

use App\Models\User;

test('guests are redirected to login when accessing api docs', function () {
    $response = $this->get('/docs');

    $response->assertRedirect(route('login'));
});

test('authenticated users can access api docs', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/docs');

    $response->assertStatus(200);
});

test('authenticated users can access openapi spec', function () {
    $user = User::factory()->create();

    // run the command to generate the docs
    $this->artisan('scribe:generate');

    $response = $this->actingAs($user)->get('/docs.openapi');

    $response->assertSuccessful();
});
