<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int        $id
 * @property string     $city
 * @property Carbon     $date
 * @property float|null $temp_current
 * @property float      $temp_max
 * @property float      $temp_min
 * @property float      $precipitation
 * @property int        $weather_code
 * @property string     $icon
 * @property string     $description
 * @property ?Carbon    $created_at
 * @property ?Carbon    $updated_at
 */
class WeatherHistory extends Model
{
    protected $guarded = ['id'];
}
