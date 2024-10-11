# TransformersPHP Background Removal Tool

This project is a Laravel-based web application that demonstrates the power of [TransformersPHP](https://github.com/CodeWithKyrian/transformers-php) for image processing tasks. It allows users to upload images and automatically remove the background using machine learning models, all within a PHP environment.

## Features

- Image upload functionality
- Background removal using the BRIA Background Removal v1.4 model
- Real-time processing updates using Laravel Reverb
- Interactive image comparison view
- Downloadable results

## Technologies Used

- [Laravel](https://laravel.com/) - The PHP framework for web artisans
- [TransformersPHP](https://github.com/CodeWithKyrian/transformers-php) - Run Hugging Face models in PHP
- [Livewire](https://livewire.laravel.com/) - Full-stack framework for Laravel
- [Laravel Reverb](https://laravel.com/docs/11.x/reverb) - WebSocket server for Laravel
- [TailwindCSS](https://tailwindcss.com/) - A utility-first CSS framework

## Prerequisites

- PHP 8.1+
- Composer
- Node.js and npm
- FFI extension enabled in PHP
- (Optional) JIT compilation enabled for better performance

## Installation

1. Clone the repository:
   ```
   git clone https://github.com/CodeWithKyrian/transformersphp-background-removal.git
   cd transformersphp-background-removal
   ```

2. Install PHP dependencies:
   ```
   composer install
   ```

3. Install JavaScript dependencies:
   ```
   npm install
   ```

4. Copy the `.env.example` file to `.env` and configure your environment variables:
   ```
   cp .env.example .env
   ```

5. Generate an application key:
   ```
   php artisan key:generate
   ```

6. Create a symbolic link for storage:
   ```
   php artisan storage:link
   ```

7. Download the BRIA Background Removal model:
   ```
   ./vendor/bin/transformers download briaai/RMBG-1.4
   ```

## Running the Application

1. Start the Laravel development server:
   ```
   php artisan serve
   ```

2. Compile assets:
   ```
   npm run dev
   ```

3. Start the queue worker:
   ```
   php artisan queue:work
   ```

4. Start the Reverb WebSocket server:
   ```
   php artisan reverb:start
   ```

5. Visit `http://localhost:8000` in your browser to use the application.

## How It Works

1. Users upload an image through the web interface.
2. The application dispatches a background job to process the image.
3. TransformersPHP loads the BRIA Background Removal model and processes the image.
4. Real-time updates are sent to the user interface using Laravel Reverb.
5. The processed image is displayed alongside the original for comparison.
6. Users can download the processed image or start over with a new image.

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Acknowledgements

- [TransformersPHP](https://github.com/CodeWithKyrian/transformers-php) for making it possible to run Hugging Face models in PHP.
- [BRIA AI](https://huggingface.co/briaai/RMBG-1.4) for the background removal model.
- The Laravel and Livewire communities for their excellent documentation and resources.
