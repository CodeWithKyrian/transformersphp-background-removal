<?php

use Illuminate\Support\Facades\Storage;
use function Livewire\Volt\{state, usesFileUploads};
use App\Jobs\RemoveImageBackground;
use function Codewithkyrian\Transformers\Pipelines\pipeline;
use Codewithkyrian\Transformers\Generation\Streamers\WhisperTextStreamer;

usesFileUploads();

state(['audio', 'transcription', 'isTranscribing' => false]);

ini_set('memory_limit', '1G');

$updatedAudio = function () {
    $this->transcription = null;
    $this->isTranscribing = false;
};

$clearAudio = function () {
    $this->audio = null;
    $this->transcription = null;
    $this->isTranscribing = false;
};

$startTranscription = function () {
    $this->isTranscribing = true;
    $this->stream(to: 'transcript', content: '', replace: true);

    $name = pathinfo($this->audio->getClientOriginalName(), PATHINFO_FILENAME);
    $extension = $this->audio->getClientOriginalExtension();
    $uploadName = $name.'_'.time().'.'.$extension;

    $uploadPath = $this->audio->storeAs('audio', $uploadName, 'public');
    $uploadFullPath = Storage::disk('public')->path($uploadPath);

    $transcriber = pipeline('asr', 'Xenova/whisper-tiny.en');
    $streamer = WhisperTextStreamer::make()->onStream(function ($text) {
        $this->stream(to: 'transcript', content: $text, replace: true);
    });

    $output = $transcriber($uploadFullPath, maxNewTokens: 256, chunkLengthSecs: 24, streamer: $streamer);

    $this->transcription = $output['text'];
    $this->isTranscribing = false;
};
?>

<div class="w-full max-w-3xl px-4 py-8 mx-auto">
    <h1 class="mb-6 text-3xl font-bold text-center text-indigo-600">TransformersPHP Audio Transcription</h1>

    @if (!$audio)
        <div class="mb-8" x-data="{ uploading: false, progress: 0 }" x-on:livewire-upload-start="uploading = true"
             x-on:livewire-upload-finish="uploading = false" x-on:livewire-upload-cancel="uploading = false"
             x-on:livewire-upload-error="uploading = false"
             x-on:livewire-upload-progress="progress = $event.detail.progress">
            <label for="audio-upload" class="text-xl font-semibold text-gray-800">Upload Audio File</label>
            <label for="audio-upload"
                   class="flex justify-center px-6 pt-5 pb-6 mt-4 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer">
                <div class="space-y-1 text-center">
                    <svg class="w-12 h-12 mx-auto text-gray-400" stroke="currentColor" fill="none"
                         viewBox="0 0 48 48" aria-hidden="true">
                        <path
                            d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <div class="flex text-sm text-gray-600">
                        <div class="relative font-medium text-indigo-600 rounded cursor-pointer hover:text-indigo-500">
                            <span>Upload a file</span>
                            <input id="audio-upload" name="audio-upload" type="file" class="sr-only"
                                   wire:model="audio">
                        </div>
                        <p class="pl-1">or drag and drop</p>
                    </div>
                    <p class="text-xs text-gray-500">MP3, WAV, or M4A up to 10MB</p>
                </div>
            </label>
            <div x-show="uploading">
                <progress max="100" x-bind:value="progress"></progress>
            </div>
        </div>
    @else
        <div class="mb-8">
            <h2 class="mb-2 text-xl font-semibold text-gray-800">Uploaded Audio</h2>
            <audio controls class="w-full">
                <source src="{{ $audio->temporaryUrl() }}" type="audio/mpeg">
                Your browser does not support the audio element.
            </audio>
        </div>

        <div class="grid grid-cols-2 gap-4 mb-8">
            <button wire:click="startTranscription"
                    class="w-full px-4 py-2 text-white bg-indigo-600 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                {{ $isTranscribing ? 'disabled' : '' }}>
                {{ $isTranscribing ? 'Transcribing...' : 'Start Transcription' }}
            </button>
            <button wire:click="clearAudio"
                    class="w-full px-4 py-2 text-white bg-red-600 rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                Clear Audio
            </button>
        </div>

        <div>
            <h2 class="mb-2 text-xl font-semibold text-gray-800">Transcript</h2>
            <div class="bg-white shadow-sm rounded-lg p-4 min-h-[200px]">
                <p class="text-gray-700" wire:stream="transcript">
                    {{ $transcription ?? 'Your transcript will appear here...' }}
                </p>
            </div>
        </div>
    @endif
</div>
