<?php

namespace App\Models;

use DateTimeInterface;
use App\Helpers\Utilities;
use App\Models\Subcategories;
use Illuminate\Database\Eloquent\Model;

class Movies extends Model
{
    protected $guarded =[];
    protected $hidden = [
        'is_deleted', 'image_poster', 'image_mobile', 'image_slider'
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d h:i:s');
    }
    
    //functions to return full image url
    protected $appends = ['imagePosters', 'imageMobiles','imageSliders'];
    public function getImagePostersAttribute(){
        return ($this->image_poster != null)? request()->get('host') . Utilities::$imageBuket . $this->image_poster:null;
    }
    public function getimageMobilesAttribute(){
        return ($this->image_mobile != null)? request()->get('host') . Utilities::$imageBuket . $this->image_mobile:null;
    }
    public function getimageSlidersAttribute(){
        return ($this->image_slider != null)? request()->get('host') . Utilities::$imageBuket . $this->image_slider:null;
    }

    //relations
    public function videoObject(){
        return $this->hasOne(VideosObjects::class, 'videoable_id')->where('videoable_type', 'Movies');
    }
    public function videoObjects(){
        return $this->hasOne(VideosObjects::class, 'videoable_id')->where('videoable_type', 'Movies')->whereHas('resolutionUrl');
    }
    public function gender(){
        return $this->belongsTo(Genders::class, 'gender_id')->where('is_deleted', 0);
    }
    public function user(){
        return $this->belongsTo(User::class, 'user_id')->where('is_deleted', 0);
    }
    public function age_range(){
        return $this->belongsTo(AgeRange::class, 'age_range_id')->where('is_deleted', 0);
    }
    public function subcategory(){
        return $this->belongsTo(Subcategories::class, 'subcategory_id')->where('is_deleted', 0);
    }
}
