<?php

use function Livewire\Volt\{state, usesFileUploads};
use App\Jobs\RemoveImageBackground;

usesFileUploads();

state(['image']);

$updatedImage = function () {
    $name = pathinfo($this->image->getClientOriginalName(), PATHINFO_FILENAME);
    $extension = $this->image->getClientOriginalExtension();
    $uploadName = $name.'_'.time().'.'.$extension;

    $uploadPath = $this->image->storeAs('images', $uploadName, 'public');

    $imageId = base64url_encode($uploadPath);

    RemoveImageBackground::dispatch($imageId);

    $this->redirectRoute('results', ['id' => $imageId]);
};
?>

<div class="px-4 py-8">
    <h1 class="mb-4 text-center text-4xl font-bold text-indigo-900">TransformersPHP Background Removal Tool</h1>
    <p class="mb-8 text-center text-lg">Upload an image to remove the background.</p>

    <div class="mb-8 flex items-center justify-center w-full max-w-2xl mx-auto">
        <label for="dropzone-file"
               class="flex flex-col items-center justify-center w-full aspect-video border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-200 hover:bg-gray-100">
            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                <svg class="w-8 h-8 mb-4 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                     fill="none" viewBox="0 0 20 16">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"/>
                </svg>
                <p class="mb-2 text-sm text-gray-500"><span class="font-semibold">Click to upload</span> or drag and
                    drop</p>
                <p class="text-xs text-gray-500">SVG, PNG, JPG or GIF (MAX. 800x400px)</p>
            </div>
            <input id="dropzone-file" type="file" class="hidden" wire:model="image"/>
        </label>
    </div>
</div>