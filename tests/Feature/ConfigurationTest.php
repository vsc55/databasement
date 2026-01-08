<?php

use App\Models\User;

test('guests are redirected to login', function () {
    $response = $this->get(route('configuration.index'));
    $response->assertRedirect(route('login'));
});

test('authenticated users can access configuration page', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('configuration.index'));

    $response->assertStatus(200)
        ->assertSee('Configuration')
        ->assertSee('BACKUP_WORKING_DIRECTORY')
        ->assertSee('MYSQL_CLI_TYPE');
});
