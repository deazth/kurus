<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\CourseApplicationRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use \Carbon\Carbon;

/**
 * Class CourseApplicationCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class CourseApplicationCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation {store as traitStore;}
    // use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\CourseApplication::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/course-application');
        CRUD::setEntityNameStrings('course application', 'course applications');
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
      CRUD::addClause('where', 'user_id', '=', backpack_user()->id);
        // CRUD::column('name')->type('text');
        CRUD::addColumn(['name' => 'Course.name', 'type' => 'text', 'label' => 'Course Name']);
        CRUD::addColumn(['name' => 'Course.start_date', 'type' => 'text', 'label' => 'Start Date']);
        CRUD::addColumn(['name' => 'Course.end_date', 'type' => 'text', 'label' => 'End Date']);
        CRUD::addColumn(['name' => 'is_free', 'type' => 'boolean']);
        CRUD::addColumn(['name' => 'created_at', 'type' => 'datetime', 'label' => 'Added At']);

        /**
         * Columns can be defined using the fluent syntax or array syntax:
         * - CRUD::column('price')->type('number');
         * - CRUD::addColumn(['name' => 'price', 'type' => 'number']);
         */
    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(CourseApplicationRequest::class);

        // get current user's info
        $userinfo = backpack_user()->UserInfo();


        CRUD::addField([
           'name' => 'Course',
           'label' => 'Select a course to participate',
           'type' => 'select',
           'entity'    => 'Course',
           'model'     => "App\Models\Course",
           'allows_null' => false,
           'attribute' => 'name_date',
           'options'   => (function ($query) {
              $today = new Carbon;
              return $query->whereDate('start_date', '>', $today->toDateTimeString())->get();
            }),
         ]);

        CRUD::addField([
          'name' => 'user_id',
          'type' => 'hidden',
          'value' => $userinfo->user_id
        ]);

        // show claim free course
        if($userinfo->free_eligible > 0){
          CRUD::addField([
            'name' => 'is_free',
            'type' => 'checkbox',
            'label' => 'Claim Free Course'
          ]);
        }

        /**
         * Fields can be defined using the fluent syntax or array syntax:
         * - CRUD::field('price')->type('number');
         * - CRUD::addField(['name' => 'price', 'type' => 'number']));
         */
    }

    /**
     * Define what happens when the Update operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }

    public function store(){
      $req = $this->crud->getRequest();
      // dd($req->all());
      $userinfo = backpack_user()->UserInfo();
      $free_setting = \Setting::get('free_course_count');
      if($req->filled('is_free') && $req->is_free == 1){
        if($userinfo->free_eligible == 0){
          abort(402);
        }

        $userinfo->free_eligible--;
        $userinfo->free_used++;

      } else {
        // purchased course. increase counter
        $userinfo->paid_total++;
        $userinfo->paid_counter++;
        if($userinfo->paid_counter == $free_setting){
          $userinfo->paid_counter = 0;
          $userinfo->free_eligible++;
        }
      }

      $userinfo->save();

      $response = $this->traitStore();
      return $response;
    }
}
