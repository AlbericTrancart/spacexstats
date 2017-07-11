<?php
namespace SpaceXStats\Http\Controllers;
use Illuminate\Support\Facades\Cache;

class AdminController extends Controller {
    public function index() {
        return view('admin', array(

        ));
    }
	public function clearcache(){
		Cache::flush();
		return view('admin', array(

        ));
	}
}