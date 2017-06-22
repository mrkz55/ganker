<?php namespace App\Http\Controllers;

use Backpack\CRUD\app\Http\Controllers\CrudController;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\ItemRequest as StoreRequest;
use App\Http\Requests\ItemRequest as UpdateRequest;

class ItemCrudController extends CrudController {

  public function setup() {
    $this->crud->setModel("App\Item");
    $this->crud->setRoute("items");
    $this->crud->setEntityNameStrings('item', 'items');

    $this->crud->setColumns(['name']);
    $this->crud->addField([
                          'name' => 'name',
                          'label' => "Tag name"
                          ]);

    $this->crud->enableAjaxTable(['title', 'category']);

  }

  public function store(StoreRequest $request)
  {
    return parent::storeCrud();
  }

  public function update(UpdateRequest $request)
  {
    return parent::updateCrud();
  }
}