<?php

use Illuminate\Foundation\Events\DiagnosingHealth;
use Illuminate\Support\Facades\Event;

test('the healthcheck endpoint returns ok and dispatches DiagnosingHealth', function () {
    Event::fake();

    $this->get('/up')
        ->assertOk()
        ->assertSee('Application up');

    Event::assertDispatched(DiagnosingHealth::class);
});

test('the healthcheck endpoint returns json status when requested', function () {
    $this->getJson('/up')
        ->assertOk()
        ->assertExactJson(['status' => 'up']);
});
