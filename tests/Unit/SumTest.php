<?php

beforeAll(function () {
    // Prepare something once before any of this file's tests run...
    include_once( dirname(__FILE__)."/../../src/core/test.php" );
});

beforeEach(function () {
    $this->o_test = new test();
});

describe('group related tests', function () {
    it('performs sums', function () {
        $result = $this->o_test->sum(1, 2);
    
        expect($result)->toBe(3);
    });

    it('performs rest', function () {
        $result = $this->o_test->rest(5, 2);
    
        expect($result)->toBe(3);
    });
});

afterEach(function () {
    $this->o_test = null;
});

afterAll(function () {
    // Clean testing data after all tests run...
});