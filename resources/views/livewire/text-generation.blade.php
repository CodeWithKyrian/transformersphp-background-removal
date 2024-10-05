<?php

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Jobs\RemoveImageBackground;
use Codewithkyrian\Transformers\Generation\Streamers\TextStreamer;
use Codewithkyrian\Transformers\Generation\Streamers\StreamMode;

use function Livewire\Volt\{state};
use function Codewithkyrian\Transformers\Pipelines\pipeline;

state([
    'prompt',
    'output',
    'isGenerating' => false,
    'messages' => [
        ['role' => 'system', 'content' => 'You are a helpful assistant.'],
    ]
]);

ini_set('memory_limit', '1G');
set_time_limit(60);

$clearPrompt = function () {
    $this->prompt = null;
    $this->output = null;
    $this->isGenerating = false;
};

$startGenerating = function () {
    $this->output = '';
    $this->isGenerating = true;
    $this->stream(to: 'generated_output', content: '', replace: true);

    $this->messages[] = ['role' => 'user', 'content' => $this->prompt];

    $generator = pipeline('text-generation', 'onnx-community/Llama-3.2-1B-Instruct', modelFilename: 'model_q4');

    $streamer = TextStreamer::make()
        ->shouldSkipPrompt()
        ->setStreamMode(StreamMode::FULL)
        ->onStream(function ($text) {
            $this->stream(to: 'generated_output', content: Str::markdown(mb_convert_encoding($text ,'UTF-8')), replace: true);
        });

    $tokens = $generator->tokenizer->applyChatTemplate($this->messages, addGenerationPrompt: true);

    [$outputMessages] = $generator($this->messages, maxNewTokens: 256, streamer: $streamer, temperature: 0.6, doSample: true, topP: 0.9);

    $this->messages = $outputMessages['generated_text'];

    $this->output =  $this->messages[count( $this->messages) - 1]['content'];

    $this->isGenerating = false;
};
?>

<div class="w-full max-w-3xl px-4 py-8 mx-auto">
    <h1 class="mb-6 text-3xl font-bold text-center text-indigo-600">TransformersPHP Text Generation</h1>

    <form wire:submit.prevent="startGenerating" class="mb-8">
        <label for="input-group-1" class="block mb-2 text-sm font-medium text-gray-900">Enter Prompt</label>
        <div class="relative mb-6">
            <input
                type="text"
                wire:model="prompt"
                wire:loading.class="!bg-gray-200"
                wire:target="startGenerating"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pe-10 p-4"
                placeholder="Enter your prompt">
            <div class="absolute inset-y-0 end-0 flex items-center pe-3.5 pointer-events-none">
                <svg wire:loading.remove class="size-6 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                     stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 0 0-2.456 2.456ZM16.894 20.567 16.5 21.75l-.394-1.183a2.25 2.25 0 0 0-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 0 0 1.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 0 0 1.423 1.423l1.183.394-1.183.394a2.25 2.25 0 0 0-1.423 1.423Z"/>
                </svg>
                <svg wire:loading aria-hidden="true" class="size-6 text-gray-200 animate-spin fill-indigo-600" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor"/>
                    <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill"/>
                </svg>
            </div>
        </div>
    </form>

    <div>
        <h2 class="mb-2 text-xl font-semibold text-gray-800">Results</h2>
        <div class="border-2 border-gray-300 border-dashed rounded-lg p-4 min-h-[200px] text-gray-700 prose max-w-none">
            <div wire:stream="generated_output">
                @if($output)
                    {!!  Str::markdown(mb_convert_encoding($output ,'UTF-8')) !!}
                @else
                    <span class="text-gray-400">The generated text will appear here...</span>
                @endif
            </div>
        </div>
    </div>
</div>
