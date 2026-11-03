<?php

use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "pest()" function to bind a different classes or traits.
|
*/

pest()->extend(TestCase::class)
  ->use(Illuminate\Foundation\Testing\RefreshDatabase::class)
    ->in('Feature', 'Unit', 'Browser');

/*
|--------------------------------------------------------------------------
| Feature Tests
|--------------------------------------------------------------------------
|
| Page renders go through the Inertia layout, which looks up Vite entries.
| CI does not build assets for this suite, and the dev server hot file is
| what makes the same tests pass locally.
|
*/

uses()->beforeEach(function () {
    $this->withoutVite();
})->in('Feature');

/*
|--------------------------------------------------------------------------
| Browser Tests
|--------------------------------------------------------------------------
|
| Ziggy v1 keeps a process-wide "already emitted" flag. Pest's browser
| cleaner resets Tighten\Ziggy (v2), so it never clears Tightenco\Ziggy.
| ResetZiggy runs on each request and is what keeps later visits working.
|
*/

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

function something()
{
    // ..
}
