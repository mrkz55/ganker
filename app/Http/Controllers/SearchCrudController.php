<?php namespace App\Http\Controllers;

use Backpack\CRUD\app\Http\Controllers\CrudController;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\SearchRequest as StoreRequest;
use App\Http\Requests\SearchRequest as UpdateRequest;

class SearchCrudController extends CrudController {

  public function setup() {
    $this->crud->setModel("App\Search");
    $this->crud->setRoute("search");
    $this->crud->setEntityNameStrings('search', 'searches');

    //$this->crud->setColumns(['category' => 'Category', 'search_term' => 'Search Term']);

    $this->crud->addColumn([
                           'name' => 'category_name',
                           'label' => "Category",
                           'type' => 'text',

                           ]);

    $this->crud->addColumn([
                           'name' => 'search_term',
                           'label' => "Search Term",
                           'type' => 'text',
                           ]);

    $this->crud->addColumn([
                           'name' => 'status',
                           'label' => "Status",
                           'type' => 'text',
                           ]);

    $this->crud->addField([  
                          'label' => "Category",
                          'type' => 'select2',
                          'name' => 'category_id', 
                          'entity' => 'category',
                          'attribute' => 'name', 
                          'model' => "App\Category"
                          ]);

    $this->crud->addField([
                          'name' => 'search_term',
                          'label' => "Search Term",
                          'hint' => 'ie LED'
                          ]);

  }

  public function store(StoreRequest $request)
  {

    $return = parent::storeCrud();

    $category_slug = $this->crud->entry->category->slug;
    $user_slug = false;
    $search_term = $this->crud->entry->search_term;

    $this->crud->model::searchEbay($category_slug, $user_slug, $search_term);

    return $return;

  }

  public function update(UpdateRequest $request)
  {
    return parent::updateCrud();
  }
}