<?php

use App\Models\User;

test('guests are redirected to login when accessing api docs', function () {
    $response = $this->get('/docs/api');

    $response->assertRedirect(route('login'));
});

test('authenticated users can access api docs', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/docs/api');

    $response->assertStatus(200);
});

test('authenticated users can access openapi spec', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/docs/api.json');

    $response->assertSuccessful();
    $response->assertJsonStructure([
        'openapi',
        'info' => ['title', 'version'],
        'paths',
    ]);
});
