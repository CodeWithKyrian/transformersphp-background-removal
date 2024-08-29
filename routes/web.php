<?php

use Livewire\Volt\Volt;

Volt::route('/', 'homepage')->name('homepage');
Volt::route('/results/{id}', 'results')->name('results');
