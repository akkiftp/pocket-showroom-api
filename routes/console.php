<?php

use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment('Pocket Showroom API');
})->purpose('Display an inspiring quote');
