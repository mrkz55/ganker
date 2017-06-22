<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

use Backpack\CRUD\CrudTrait;
use App\Traits\EbayScraperTrait;

class Category extends Model
{

	use CrudTrait, EbayScraperTrait, Sluggable;

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable()
    {
        return [
            'slug' => [
                'source' => 'name'
            ]
        ];
    }

	protected $table = 'categories';
	protected $primaryKey = 'id';
	public $timestamps = true;
	protected $fillable = ['id', 'name', 'slug'];

	public function items()
	{
		return $this->hasMany('App\Item');
	}

}
