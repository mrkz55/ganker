<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

use Backpack\CRUD\CrudTrait;
use App\Traits\EbayScraperTrait;

class Seller extends Model
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

	protected $table = 'sellers';
	protected $primaryKey = 'id';
	public $timestamps = true;
	protected $fillable = ['id', 'name', 'feedback', 'location', 'cover', 'avatar', 'slug'];

	public function items()
	{
		return $this->hasMany('App\Models\Item');
	}

}
