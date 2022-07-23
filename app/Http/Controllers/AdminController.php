<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use \Carbon\Carbon;
use Backpack\CRUD\app\Library\Widget;

class AdminController extends Controller
{
  protected $data = []; // the information we send to the view

  /**
   * Create a new controller instance.
   */
  public function __construct()
  {
      $this->middleware(backpack_middleware());
  }

  /**
   * Show the admin dashboard.
   *
   * @return \Illuminate\Http\Response
   */
  public function dashboard()
  {
      $this->data['title'] = trans('backpack::base.dashboard'); // set the page title
      $this->data['breadcrumbs'] = [
          trans('backpack::crud.admin')     => backpack_url('dashboard'),
          trans('backpack::base.dashboard') => false,
      ];

      $nnow = new Carbon;
      $upcoming = Course::where('start_date', '>', $nnow->toDateTimeString())->get();
      $this->data['upcoming'] = $upcoming;

      $info = backpack_user()->UserInfo();

      if($info->free_eligible > 0){
        Widget::add([
          'type'        => 'progress',
          'class'       => 'card text-white mb-2 bg-primary',
          'value'       => $info->free_eligible,
          'description' => 'Available free courses',
          'progress'    => 100, // integer
          'hint'        => 'You can claim free course',
          'wrapper' => [
            'class' => 'col-md-6 m-0 p-0', // customize the class on the parent element (wrapper)
          ]
        ])->to('before_content');
      }

      return view(backpack_view('dashboard'), $this->data);
  }

  /**
   * Redirect to the dashboard.
   *
   * @return \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
   */
  public function redirect()
  {
      // The '/admin' route is not to be used as a page, because it breaks the menu's active state.
      return redirect(backpack_url('dashboard'));
  }
}
