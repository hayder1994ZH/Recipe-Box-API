<?php
namespace App\Helpers;

use App\Models\Views;
use App\Models\Channels;
use App\Models\LangBodys;
use App\Models\Languages;
use App\Models\BlockVideos;
use App\Models\Restaurants;
use Illuminate\Support\Str;
use GuzzleHttp\Psr7\Request;
use App\Models\Subcategories;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class Utilities {

    public static function auth()//check auth
    {
        if(Auth::check()){
           return auth()->user()->load('rules');
        }
        return null;
    }

    public static function wrap($data, $code)//get response with status code
    {
        return response()->json($data, $code);
    }

    public static function wrapStatus($data, int $httpCode)//get response with status code
    {
        return response()->json($data, $httpCode);
    }

    public static function uploadImage($image)//upload images
    {
       return $image->store('');
    }

}
