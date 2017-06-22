<?php namespace App\Http\Controllers;

use Backpack\CRUD\app\Http\Controllers\CrudController;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\SellerRequest as StoreRequest;
use App\Http\Requests\SellerRequest as UpdateRequest;

class SellerCrudController extends CrudController {

  public function setup() {
    $this->crud->setModel("App\Seller");
    $this->crud->setRoute("sellers");
    $this->crud->setEntityNameStrings('seller', 'sellers');

    $this->crud->setColumns(['name']);

    //generate a random primary key, we'll update later with the real ID when we scrape the store
    $this->crud->addField([
                          'type' => 'hidden',
                          'name' => 'id',
                          'value' => rand(0, 9)^3]);
    $this->crud->addField([
                          'name' => 'name',
                          'label' => "Seller name",
                          'hint' => 'ie streetfx-motorsport'
                          ]);
  }

  public function store(StoreRequest $request)
  {

    $this->crud->model::scrapeEbayStore($request->get('name'));

    return parent::storeCrud();
  }

  public function update(UpdateRequest $request)
  {

    $this->crud->model::scrapeEbayStore($request->get('name'));

    return parent::updateCrud();
  }

}