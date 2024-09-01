<?php

require_once 'vendor/autoload.php';

test('send method handles GET request correctly', function () {
    $result = request::send([
        'method' => 'GET',
        'url' => 'http://localhost/labs/dds-open-api/products/info',
        //'data' => ['param1' => 'value1'],
    ]);

    expect($result)->toBeArray()
        ->toHaveKey('error')
        ->toHaveKey('result');
});

test('send method handles POST request correctly', function () {
    $result = request::send([
        'method' => 'POST',
        'url' => 'http://localhost/labs/dds-open-api/products',
        //'data' => ['param1' => 'value1'],
        //'send_type' => 'JSON',
    ]);

    expect($result)->toBeArray()
        ->toHaveKey('error')
        ->toHaveKey('result');
});

test('send method handles custom headers', function () {
    $result = request::send([
        'method' => 'GET',
        'url' => 'http://localhost/labs/dds-open-api/products/info',
        //'headers' => ['Authorization' => 'Bearer token123'],
    ]);

    expect($result)->toBeArray()
        ->toHaveKey('error')
        ->toHaveKey('result');
});

test('send method handles file download', function () {
    $tempFile = "C:\data\wamp64\www\labs\dds-open-api\upld\media_file\\0\\3290\\test.zip";
    $result = Request::send([
        'method' => 'GET',
        'url' => 'https://example.com/file.zip',
        'return_type' => 'file',
        'return_file' => $tempFile,
    ]);

    expect($result)->toBeArray()
        ->toHaveKey('error')
        ->toHaveKey('result');
    expect(file_exists($tempFile))->toBeTrue();
});

test('send method handles errors', function () {
    $result = request::send([
        'method' => 'GET',
        'url' => 'https://non-existent-url.com',
    ]);

    expect($result['error']['flag'])->toBeTrue();
    expect($result['error']['description'])->not->toBeEmpty();
});
