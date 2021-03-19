<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Material extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'part_number',
        'name',
        'description',
        'acquisition_cost',
        'price',
        'stock',
        'unit_id',
        'color_id',
        'size_id',
    ];


    /**
     * Get the color associated with the Material.
     */
    public function color()
    {
        return $this->belongsTo(Color::class)->withTrashed();
    }

    /**
     * Get the size associated with the Material.
     */
    public function size()
    {
        return $this->belongsTo(Size::class)->withTrashed();
    }

    /**
     * Get the unit associated with the Material.
     */
    public function unit()
    {
        return $this->belongsTo(Unit::class)->withTrashed();
    }



}
