<?php

it('can boot the package service provider', function () {
    $this->assertTrue(app()->bound('passwordless'));
});
