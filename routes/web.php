<?php

use Livewire\Volt\Volt;

Volt::route('/', 'homepage')->name('homepage');
Volt::route('/results/{id}', 'results')->name('results');
Volt::route('speech-to-text', 'speech-to-text')->name('speech-to-text');
Volt::route('text-generation', 'text-generation')->name('text-generation');
