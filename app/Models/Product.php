<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Dyrynda\Database\Support\CascadeSoftDeletes;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Support\Str;
use Carbon\Carbon;

class Product extends Model
{
    use HasFactory, SoftDeletes, CascadeSoftDeletes, Sluggable;

    protected $cascadeDeletes = ['children'];

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name'
            ]
        ];
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'slug',
        'code',
        'price',
        'file_name',
        'description',
        'color_id',
        'size_id',
        'parent_id',
        'sort',
        'status',
    ];


    public function getDescriptionLimitedAttribute()
    {
        return Str::words($this->description, '15');
    }
    
    public function size()
    {
        return $this->belongsTo(Size::class, 'size_id')->withTrashed();
    }

    public function color()
    {
        return $this->belongsTo(Color::class, 'color_id')->withTrashed();
    }

    /**
     * @return mixed
     */
    public function parent()
    {
        return $this->belongsTo(Product::class, 'parent_id')->with('parent');
    }

    /**
     * @return mixed
     */
    public function children()
    {
        return $this->hasMany(Product::class, 'parent_id')->with('children', 'size', 'color');
    }

    public function getTotalStock()
    {
        return $this->children->sum(function($parent) {
          return $parent->stock + $parent->stock_revision + $parent->stock_store;
        });
    }


    public static function boot()
    {
        parent::boot();

        // cause a restore of a folder to cascade
        // to children so they are also restored
        static::restoring(function($restore_subproducts) {
            // $restore_subproducts->children->withTrashed()->get()
            //     ->each(function($subprod) {
            //         $subprod->restore();
            //     });

            $restore_subproducts->children()->withTrashed()->restore();
        });
    }

}
