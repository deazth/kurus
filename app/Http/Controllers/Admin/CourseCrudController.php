<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\CourseRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class CourseCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class CourseCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\Course::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/course');
        CRUD::setEntityNameStrings('course', 'courses');
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {


        /**
         * Columns can be defined using the fluent syntax or array syntax:
         * - CRUD::column('price')->type('number');
         * - CRUD::addColumn(['name' => 'price', 'type' => 'number']);
         */

        CRUD::addColumn(['name' => 'CourseCategory.name', 'type' => 'text', 'label' => 'Category']);
        CRUD::addColumn(['name' => 'name', 'type' => 'text']);
        CRUD::addColumn(['name' => 'status', 'type' => 'text']);
        CRUD::addColumn(['name' => 'start_date', 'type' => 'text']);
        CRUD::addColumn(['name' => 'end_date', 'type' => 'text']);
    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(CourseRequest::class);

        CRUD::addField([
           'name' => 'CourseCategory',
           'label' => 'Course Category',
           'type' => 'select',
           'entity'    => 'CourseCategory',
           'model'     => "App\Models\CourseCategory",
           'attribute' => 'name',
           'wrapper'   => [
              'class'      => 'col-md-6',
            ],
         ]);

         CRUD::addField([
            'name' => 'status',
            'label' => 'Status',
            'type' => 'select_from_array',
            'options' => [
              'Active' => 'Active',
              'Ended' => 'Ended',
              'Cancelled' => 'Cancelled'
            ],
            'allows_null' => false,
            'wrapper'   => [
               'class'      => 'col-md-6',
             ],
          ]);

        CRUD::addField(['name' => 'name', 'type' => 'text']);
        CRUD::addField(['name' => 'extra_info', 'type' => 'textarea']);

        CRUD::addField(['name' => 'start_date', 'type' => 'date',
        'wrapper'   => [
           'class'      => 'col-md-6',
         ],]);
        CRUD::addField(['name' => 'end_date', 'type' => 'date',
        'wrapper'   => [
           'class'      => 'col-md-6',
         ],]);


         // CRUD::addField([
         //   'name' => 'banner',
         //    'label' => 'Banner Image',
         //    'type'      => 'upload',
         //    'upload'    => true,
         //    'disk'      => 'local',
         //  ]);
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
}
