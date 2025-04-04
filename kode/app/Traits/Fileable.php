<?php

namespace App\Traits;

use App\Models\Core\File as CoreFile;
use Illuminate\Support\Arr;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\File;
use App\Enums\StorageKey;
use Illuminate\Http\UploadedFile;
trait Fileable
{

    /**
     * Store a file & image
     *
     * @param  $file
     * @param string $location
     * @param string $size
     * @param CoreFile|null $removeFile
     * @param string $name
     * @return array
     */
    public function storeFile(UploadedFile $file, string $location, ?string $size = null, ?CoreFile $removeFile = null, ?string $name = null): array
    {


        $name = uniqid() . time() . '.' . $file->getClientOriginalExtension();
        $imagePath = $location . '/' . $name;
        $status = true;
        $disk = site_settings('storage');
        $inputFile = $file;

        //remove file if exists
        if ($removeFile)
            $this->unlink($location, $removeFile);
        switch ($disk) {
            case StorageKey::LOCAL->value:
                if (!file_exists($location))
                    mkdir($location, 0755, true);
                switch (substr($file->getMimeType(), 0, 5)) {
                    case 'image':
                        $image = Image::make(file_get_contents($file));
                        if (isset($size)) {
                            list($width, $height) = explode('x', strtolower($size));
                            $image->resize($width, $height, function ($constraint): void {
                                $constraint->aspectRatio();
                            });
                        }
                        $image->save($imagePath);
                        break;

                    default:

                        $file->move($location, $name);
                        break;
                }
                break;

            default:
                $this->{Arr::get(StorageKey::toArray(), strtoupper($disk))}();
                \Storage::disk($disk)->putFileAs(
                    $location,
                    $file,
                    $name
                );
                break;
        }

        $size = 20000;
        try {
            $size = @$inputFile->getSize();
        } catch (\Throwable $th) {

        }



        return [
            'status' => $status,
            'name' => $name,
            'disk' => site_settings('storage'),
            "size" => $this->formatSize($size),
            "extension" => strtolower($file->getClientOriginalExtension())
        ];

    }



    /**
     * Get file size
     *
     * @param  int|string $bytes
     * @return string
     */
    public function formatSize(string|int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        for ($i = 0; (int) $bytes >= 1024 && $i < 4; $i++) {
            $bytes /= 1024;
        }
        return round($bytes, 2) . ' ' . $units[$i];
    }




    /**
     * Unlink File Or Image
     *
     * @param string $location
     * @param CoreFile|null $file
     * @return bool
     */
    public function unlink(string $location, ?CoreFile $file = null): bool
    {


        try {
            switch (@$file->disk) {
                case StorageKey::LOCAL->value:
                    if (file_exists($location . '/' . @$file->name) && is_file($location . '/' . @$file->name))
                        @unlink($location . '/' . @$file->name);
                    break;

                default:
                    $this->{Arr::get(StorageKey::toArray(), strtoupper($file->disk))}();
                    if (Storage::disk($file->disk)->exists($location . '/' . @$file->name))
                        Storage::disk($file->disk)->delete($location . '/' . @$file->name);
                    break;
            }

            @$file->delete();


        } catch (\Exception $ex) {
            return false;
        }

        return true;
    }


    /**
     * Set aws configuration
     *
     * @return void
     */
    public function setAWSConfig(): void
    {

        $awsConfig = json_decode(site_settings('aws_s3'), true);

        config(
            [
                'filesystems.disks.s3.key' => Arr::get($awsConfig, 's3_key'),
                'filesystems.disks.s3.secret' => Arr::get($awsConfig, 's3_secret'),
                'filesystems.disks.s3.region' => Arr::get($awsConfig, 's3_region'),
                'filesystems.disks.s3.bucket' => Arr::get($awsConfig, 's3_bucket'),
                'filesystems.disks.s3.use_path_style_endpoint' => false,
            ]
        );
    }




    /**
     * set ftp configuration
     *
     * @return void
     */
    public function setFTPConfig(): void
    {

        $ftpConfig = json_decode(site_settings('ftp'), true);
        config(
            [
                'filesystems.disks.ftp.host' => Arr::get($ftpConfig, 'host'),
                'filesystems.disks.ftp.username' => Arr::get($ftpConfig, 'user_name'),
                'filesystems.disks.ftp.password' => Arr::get($ftpConfig, 'password'),
                'filesystems.disks.ftp.port' => (int) Arr::get($ftpConfig, 'port'),
                'filesystems.disks.ftp.root' => Arr::get($ftpConfig, 'root')
            ]
        );

    }

    /**
     * get base URL
     *
     * @param string $path
     * @param array $config
     * @return array|null
     */
    function getBasePath(string $path, array $config): array|null
    {

        $keys = explode(',', $path);
        $current = $config;
        foreach ($keys as $key) {
            if (!Arr::exists($current, $key))
                return null;
            $current = $current[$key];
        }

        return $current;
    }



    /**
     * Get image URL
     *
     * @param CoreFile|null $image
     * @param string $path
     * @return string
     */
    public function getimageURL(CoreFile $file = null, string $path, bool $size, ?string $foreceSize = null): string
    {
        $config = config("settings")['file_path'];
        $basepath = $this->getBasePath($path, $config);
        $imageURL = asset('assets/images/default/default.jpg');

        if ($size) {
            $default = $foreceSize ?? "100x100";
            $imageURL = route('default.image', Arr::get($basepath, 'size', $default));
        }
        try {

            if (Arr::exists($basepath, 'path')) {
                $image = $basepath['path'] . "/" . @$file->name;
                switch ($file->disk) {
                    case StorageKey::LOCAL->value:
                        if (file_exists($image) && is_file($image))
                            $imageURL = asset($image);
                        break;
                    default:
                        $this->{Arr::get(StorageKey::toArray(), strtoupper($file->disk))}();
                        if (Storage::disk(@$file->disk)->exists($image))
                            $imageURL = \Storage::disk(@$file->disk)->url($image);
                        break;
                }
            }

        } catch (\Throwable $th) {

        }
        return $imageURL;
    }



    public function getFilePath(CoreFile $file = null, string $path, bool $size, ?string $forceSize = null): string
    {
        $config         = config("settings")['file_path'];
        $basepath       = $this->getBasePath($path, $config);

        try {
            if (Arr::exists($basepath, 'path')) {
                $filePath = $basepath['path'] . "/" . $file->name;

                switch ($file->disk) {
                    case StorageKey::LOCAL->value:
                        if (file_exists($filePath) && is_file($filePath)) {
                            return $filePath;
                        }
                        break;
                    default:
                        return Storage::disk($file->disk)->path($filePath);
                }
            }
        } catch (\Throwable $th) {
            return '';
        }


        return $filePath;
    }





    /**
     * Check if file exists or not
     *
     * @param string $url
     * @return boolean
     */
    public static function check_file(string $url): bool
    {
        $headers = get_headers($url);
        return (bool) preg_match('/\bContent-Type:\s+(?:image|audio|video)/i', implode("\n", $headers));
    }



    /**
     * Download a file
     *
     * @param string $location
     * @param CoreFile|null $file
     * @return mixed
     */
    public function downloadFile(string $location, ?CoreFile $file = null): mixed
    {

        $filePath = $location . '/' . $file->name;
        $URL = null;

        try {

            switch ($file->disk) {
                case StorageKey::LOCAL->value:
                    $headers = [
                        'Content-Type' => File::mimeType($filePath),
                    ];
                    $URL = Response::download($filePath, $file->name, $headers);
                    break;

                default:
                    $this->{Arr::get(StorageKey::toArray(), strtoupper($file->disk))}();

                    $headers = [
                        'Content-Disposition' => 'attachment; filename="' . $file->name . '"',
                    ];

                    $URL = Response::make(\Storage::disk($file->disk)->get($filePath), 200, $headers);
                    break;
            }


        } catch (\Throwable $th) {

        }

        return $URL;

    }






}
