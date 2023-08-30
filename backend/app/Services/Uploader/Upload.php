<?php

namespace App\Services\Uploader;


use Intervention\Image\Facades\Image;

class Upload{

    private static bool $resize = false;
    private static int $width = 0;
    private static int $height = 0;
    private static string|null $imageEncode = null;
    private static string|null $appendName = null;

    private function __construct(){}

    /**
     * Add timestamp to prev file name
     *
     * @param $file
     * @return string
     */
    private static function getName($file): string{
        $Microtime = explode(' ',microtime())[0];
        $Microtime = last(explode('.', $Microtime));
        $result = explode('.',$file->getClientOriginalName());
        $name = $result[0]. '.' . (empty(self::$imageEncode) ? last($result) : self::$imageEncode);
        return $Microtime.'-'.$name;
    }


    /**
     * Move file to directory
     *
     * @param $file
     * @param string $directory
     * @return string
     */

    private static function move($file, string $directory): string{
        $path = $file->store($directory);
        return str_replace('uploader/','',$path);
    }

    /**
     * Upload file if file dos not exist
     *
     * @param $request
     * @param string $key
     * @param string $directory
     * @param string|null $lastPath
     * @param string|null $index
     * @return string|null
     */
    public static function upload($request, string $key, string $directory, string|null $lastPath=null, string|null $index=null){

        if( $request->hasFile($key) ){
            $file = is_null($index) ? $request->file($key) : $request->file($key)[$index];
            if(self::$resize){
                $name = !empty(self::$appendName) ? self::$width."x".self::$height.'_'.self::$appendName : self::$width."x".self::$height.'_'.self::getName($file);
                $path = storage_path('app/'.$directory.'/');
                Image::make($file)
                    ->resize(self::$width, self::$height)
                    ->encode(self::$imageEncode)
                    ->save($path . $name);
                return '/storage/'.str_replace('uploader/','',$directory).'/'.$name;
            }
            return "/storage/".static::move($file, $directory);
        }
        return $lastPath;
    }

    /**
     * Sets the values for resizing the image
     *
     * @param int $width
     * @param int $height
     * @param string|null $imageEncode
     * @param string|null $appendName
     * @return Upload
     */
    public static function resizable(int $width, int $height, string|null $imageEncode=null, string|null $appendName = null){
        self::$resize = true;
        self::$width = $width;
        self::$imageEncode = $imageEncode;
        self::$height = $height;
        self::$appendName = $appendName;
        return new self();
    }

}
