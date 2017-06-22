<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

use Backpack\CRUD\CrudTrait;
use App\Traits\EbayScraperTrait;

class Item extends Model
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

    protected $table = 'items';
    protected $primaryKey = 'id';
    public $timestamps = true;
    protected $fillable = ['id', 'seller_id', 'category_id', 'store_id', 'name', 'sold', 'quantity', 'watching', 'sales_recent', 'sales_total', 'slug'];


    public function seller()
    {
    	return $this->belongsTo('App\Seller');
    }

    public function store()
    {
    	return $this->belongsTo('App\Store');
    }

    public function category()
    {
    	return $this->belongsTo('App\Category');
    }

    public function getSellerNameAttribute()
    {
    	return isset($this->seller) ? $this->seller->name : '';
    }

    public function getStoreNameAttribute()
    {
    	return isset($this->store) ? $this->store->name : '';
    }

    public function getCategoryNameAttribute()
    {
    	return isset($this->category) ? $this->category->name : '';
    }
}
