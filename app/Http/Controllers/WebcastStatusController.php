<?php 
 namespace App\Http\Controllers;

class WebcastStatusController extends Controller {

	// AJAX GET
	public function getStatus() {
		// Check if the redis values exist
		try {
			//if (Redis::exists('webcast:isLive') == 1 && Redis::exists('webcast:viewers') == 1) {
				return Response::json(Redis::hgetall('webcast'));
			//} else {
			//	return Response::json(array('isLive' => null, 'viewers' => null));
			//}
		} catch (Predis\Connection\ConnectionException $e) {
			return Response::json(array('isLive' => null, 'viewers' => null));
		}
		
	}
}