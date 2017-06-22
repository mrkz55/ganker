<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

use Backpack\CRUD\CrudTrait;
use App\Traits\EbayScraperTrait;

class Search extends Model
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
                'source' => 'search_term'
            ]
        ];
    }

	protected $table = 'searches';
	protected $primaryKey = 'id';
	public $timestamps = true;
	protected $fillable = ['category_id', 'seller_id', 'search_term', 'slug'];

    public function category()
    {
    	return $this->belongsTo('App\Category');
    }

    public function getCategoryNameAttribute()
    {
    	return isset($this->category) ? $this->category->name : '';
    }

    public function getStatusAttribute()
    {
    	return 'Active';
    }

}
