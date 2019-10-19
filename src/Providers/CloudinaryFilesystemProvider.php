<?php


namespace Ebookr\Client\Providers;


use Illuminate\Support\ServiceProvider;
use League\Flysystem\Filesystem;
use Storage;
use TheDarkKid\Flysystem\Cloudinary\CloudinaryAdapter;

class CloudinaryFilesystemProvider extends ServiceProvider
{
    public function register()
    {}

    public function boot()
    {
        Storage::extend('cloudinary', function ($app, $config) {
            $adapter = new CloudinaryAdapter($config);

            return new Filesystem($adapter);
        });
    }
}
