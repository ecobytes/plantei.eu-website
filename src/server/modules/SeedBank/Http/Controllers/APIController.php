<?php namespace Modules\Seedbank\Http\Controllers;;

//use \Caravel\Setting;
use Pingpong\Modules\Routing\Controller;
use Illuminate\Http\Request;
use Gate;


use Validator;
use GeoIp2\Database\Reader;

class APIController extends Controller {

	/*
	|--------------------------------------------------------------------------
	| Welcome Controller
	|--------------------------------------------------------------------------
	|
	| A simple json API to plantei.eu
	|
	*/

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{

	}

	/**
	 * Validate preferences form.
	 *
	 * @return Validator
	 */
	public function prefValidator(array $data)
  {
    $user = \Auth::user();
    $rules = [
      'lon' => 'required_with:lat|regex:/^-?\d+([\,]\d+)*([\.]\d+)?$/|between:-180,180',
      'lat' => 'required_with:lon|regex:/^-?\d+([\,]\d+)*([\.]\d+)?$/|between:-180,180',
      'place_name' => 'max:255|required_with:lon,lat',
    ];
    if (! $user->name == $data['name']){
      $rules['name'] = 'required|max:255|unique:users';
    }
    if (( $user->email !== $data['email']) && ($data['email'])) {
      $rules['email'] = 'sometimes|required|email|max:255|unique:users';
    }
    if ($data['password']){
      $rules['password'] = 'required|confirmed|min:6';
    }
    return Validator::make($data, $rules);
  }


	/**
	 * GET: Reply to queries for Seeds.
	 *
	 * @return Illuminate\Pagination\LengthAwarePaginator
	 */
	public function getSeeds(Request $request)
	{

		$user = \Auth::user();
		$seeds = \Caravel\Seed::where('user_id', '<>', $user->id)->where('public', true)->orderBy('updated_at', 'desc');
		$paginated = $seeds->paginate(15)->setPath('/seedbank/allseeds');

		foreach ($paginated->getCollection() as $seed)
		{
			$seed->load('family');
			$seed->load('pictures');
		}
		$part = [ 'myseeds' => true ];

		$seed_id = $request->input('seed_id', null);
		$seed = \Caravel\Seed::find($seed_id);

		$monthsTable = [];
		foreach (range(0, 11) as $number) {
			$monthsTable[$number] = false;
		}
		if ($seed) {
			$seed->load(['months', 'species', 'variety', 'family', 'pictures']);
			foreach ( $seed->months as $month) {
				$monthsTable[$month->month - 1] = true;
			}
		};

		return $paginated;

	}

	/**
	 * GET: Reply to queries for Calendar Events.
	 *
	 * @return Collection
	 */
	 public function getEvents (Request $request) {
 		/* TODO: Only return events for specific user */

		if ($request->input('_save', Null)) {
			/* TODO: Validate form */
			return "ok";
		}

 		if ( $request->input('id', Null) ) {
 			$event = \Caravel\Calendar::findOrFail($id);
 			return $event;
 		}

		if ($request->input('start', Null) && $request->input('end', Null)) {
			$events = \Caravel\Calendar::interval($request)->get();
			return $events;
		}

		// TODO: Remove function in \Caravel\User::getEvents : FAKE events if none exists
		/*if ( ! $events->count()) {
			$events = \Auth::user()->getEvents($start=$request->input('start'), $end=$request->input('end'));
		}*/

		abort(404);

	}


	/**
	 * GET: Reply to queries for Sementecas.
	 *
	 * @return Collection
	 */
	public function getSementecas (Request $request) {

      //$response = \Caravel\Sementeca::get();
      //return $response;
      //
      //TODO: sort by date; request->interval
			if ($request->input('_save', Null)) {
				$this->validate($request, [
		      'name' => 'required',
		      'lat' => 'required',
		      'lon' => 'required',
		    ], \Lang::get('seedbank::validation'));
			}
      return \Caravel\Sementeca::paginate(5);

	}

		/**
		 * GET: Reply to queries for Sementecas for array_map
		 * GeoLocation.
		 *
		 * @return Collection
		 */
		public function getSementecasGeo (Request $request) {

	      //$response = \Caravel\Sementeca::get();
	      //return $response;
	      //
	      //TODO: sort by date; request->interval
	      return \Caravel\Sementeca::all();

		}


		private function prefValidationRules($data) {

	    $user = \Auth::user();
	    $rules = [
	      'lon' => 'required_with:lat|regex:/^-?\d+([\,]\d+)*([\.]\d+)?$/|between:-180,180',
	      'lat' => 'required_with:lon|regex:/^-?\d+([\,]\d+)*([\.]\d+)?$/|between:-180,180',
	      'place_name' => 'max:255|required_with:lon,lat',
	    ];
	    if (! $user->name == $data['name']){
	      $rules['name'] = 'required|max:255|unique:users';
	    }
	    if (( $user->email !== $data['email']) && ($data['email'])) {
	      $rules['email'] = 'sometimes|required|email|max:255|unique:users';
	    }
	    if ($data['password']){
	      $rules['password'] = 'required|confirmed|min:6';
	    }
	    return $rules;

	  }
		/**
		 * POST: update user preferences
		 * GeoLocation.
		 *
		 * @return Collection
		 */
		public function postPreferences(Request  $request)
	  {
	    $user = \Auth::user();

	    $this->validate($request, $this->prefValidationRules($request->all()));

	    if (!$request->input('password')){
	      unset($request['password']);
	    } else {
	      $request['password'] = bcrypt($request->password);
	    };
	    foreach(['email', 'lat', 'lon', 'place_name'] as $field)
	    {
	      if (!$request->input($field))
	      {
	        unset($request[$field]);
	      }
	    }
	    $user->update($request->all());
	    return $user;
		}

}
