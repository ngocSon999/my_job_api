<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $repositoryPath = app_path('Repositories');

        if (File::exists($repositoryPath)) {
            $files = File::allFiles($repositoryPath);

            foreach ($files as $file) {
                $filename = $file->getFilenameWithoutExtension();

                if ($filename === 'BaseRepositoryInterface' || $filename === 'BaseRepository') {
                    continue;
                }

                if (Str::endsWith($filename, 'Repository')) {
                    $interfaceName = $filename . 'Interface';

                    $directory = $file->getRelativePath();
                    $namespace = 'App\\Repositories\\' . ($directory ? str_replace(DIRECTORY_SEPARATOR, '\\', $directory) . '\\' : '');

                    $interfaceFullClassName = $namespace . $interfaceName;
                    $repositoryFullClassName = $namespace . $filename;

                    if (interface_exists($interfaceFullClassName)) {
                        $this->app->bind($interfaceFullClassName, $repositoryFullClassName);
                    }
                }
            }
        }
    }

    public function boot(): void
    {
        //
    }
}
