<?php

use function Livewire\Volt\{action, mount, state, on};

state(['id', 'imageUrl', 'maskedImageUrl']);

mount(function ($id) {
    $this->id = $id;
    $path = base64url_decode($id);

    abort_if(!Storage::disk('public')->exists($path), 404);

    $this->imageUrl = Storage::url($path);

    if (Cache::has($id)) {
        $path = base64url_decode(Cache::get($id));
        $this->maskedImageUrl = Storage::url($path);
    }
});

on([
    'echo:{id},ImageBackgroundRemoved' => function ($payload) {
        $path = base64url_decode($payload['masked_id']);
        $this->maskedImageUrl = Storage::url($path);
    },
]);

$download = action(fn () => response()->download(public_path($this->maskedImageUrl)))
    ->renderless();

$remove = fn () => $this->redirectRoute('homepage');

?>

<div class="px-4 py-8">
    <h1 class="mb-8 text-4xl font-bold text-center text-indigo-900">TransformersPHP Background Removal Tool</h1>

    <div class="relative flex items-center justify-center w-full mb-8">
        @if ($maskedImageUrl)
            <div id="image-compare" class="relative h-[32rem]">
                <img id="masked-image" src="{{ asset($maskedImageUrl) }}" alt="Uploaded image" class="w-auto h-full">
                <img src="{{ asset($imageUrl) }}" xalt="Uploaded image" class="w-auto h-full">
                <div
                    class="absolute inset-0 keep z-[-1] pattern-rectangles pattern-gray-600 pattern-size-4 pattern-opacity-20 pattern-bg-gray-200">
                </div>
            </div>
        @else
            <div class="relative h-[32rem]">
                <img src="{{ asset($imageUrl) }}" alt="Uploaded image" class="w-auto h-full">
                <div class="absolute inset-0 flex items-center justify-center backdrop-blur-sm">
                    <img src="{{ asset('img/ai-loading.gif') }}" alt="loading..." class="w-24 h-24">
                </div>
            </div>
        @endif
    </div>

    <div class="flex justify-center px-4">
        <button class="inline-flex items-center px-6 py-4 mx-2 text-white bg-indigo-600 rounded hover:bg-indigo-700"
            wire:click="download">
            <svg class="size-4 me-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
            </svg>
            Download
        </button>
        <button class="inline-flex items-center px-6 py-4 mx-2 text-white bg-indigo-600 rounded hover:bg-indigo-700"
            wire:click="remove">
            <svg class="size-4 me-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
            </svg>
            Restart
        </button>
    </div>
</div>

@script
    <script>
        Livewire.hook('morph.added', ({
            el
        }) => {
            if (el.id !== 'image-compare') return;

            initImageCompare();
        })

        function initImageCompare() {
            const image_compare = document.getElementById('image-compare');

            if (!image_compare) return;

            const options = {
                showLabels: true,
                labelOptions: {
                    before: 'Masked',
                    after: 'Original',
                    onHover: true
                },
            }
            new ImageCompare(image_compare, options).mount();
        }

        initImageCompare();
    </script>
@endscript
