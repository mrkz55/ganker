<?php namespace App\Http\Controllers;

use Backpack\CRUD\app\Http\Controllers\CrudController;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\CategoryRequest as StoreRequest;
use App\Http\Requests\CategoryRequest as UpdateRequest;

class CategoryCrudController extends CrudController {

  public function setup() {
    $this->crud->setModel("App\Category");
    $this->crud->setRoute("categories");
    $this->crud->setEntityNameStrings('category', 'categories');

    $this->crud->setColumns(['name', 'slug']);

    $this->crud->addField([
                          'name' => 'name',
                          'label' => 'Category name',
                          'hint' => 'ie Vehicle Parts Accessories'
                          ]);
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