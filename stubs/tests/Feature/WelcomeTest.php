<?php

declare(strict_types=1);

test('welcome screen is rendered', function (): void {
    $response = $this->get('/');

    $response->assertSuccessful();
});
