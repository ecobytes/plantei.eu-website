<?php namespace Modules\Seedbank\Http\Controllers;

use Pingpong\Modules\Routing\Controller;
use Illuminate\Http\Request;
use Gate;

class SeedBankController extends Controller {
  
  public function index()
  {
    $user = \Auth::user();

    $seeds = \Caravel\Seed::orderBy('created_at', 'desc')
      ->select('id', 'sci_name', 'common_name')
      ->get()
      ->chunk(4)[0]
      ->toArray();

    $userMessages = $user->messages()->get()->sortByDesc('created_at')->chunk(4)[0]->toArray();
    $unreadmessages = 0;
    foreach($userMessages as &$m) {
      $t = array();
      if ($m['pivot']['read']){
        $t[1] = true;
        $m['pivot']['read'] = $t;
      } else {
        $unreadmessages++;
      }
    }
    return view('seedbank::home')
      ->with('seeds', $seeds)
      ->with('usermessages', $userMessages)
      ->with('unreadmessages', $unreadmessages)
      ->with('messages', \Lang::get('seedbank::messages'))
      ->with('menu', \Lang::get('seedbank::menu'))
      ->with('username', $user->name)
      ->with('active', ['home' => true]);
  }

  public function mySeeds()
  {
    // View for my seeds
    $user = \Auth::user();
    //dd($user->id);
    //$user = \Caravel\User::where('id', 1)->first();
    $seeds = $user->seeds();
    $t = [];
    foreach($seeds as $seed){

      foreach(['origin', 'polinization', 'direct', 'public', 'available'] as $key){
        if(isset($seed[$key])){
          $seed[$key] = [$seed[$key] => true];
        }
      }
      $t[] = $seed;
    }
    //dd($t);
    $transactions = $user->transactionsPending();

    return view('seedbank::myseeds')
      ->with('seeds', $t)
      ->with('transactionsBy', $transactions['asked_by']) 
      ->with('transactionsTo', $transactions['asked_to']) 
      ->with('messages', \Lang::get('seedbank::messages'))
      ->with('menu', \Lang::get('seedbank::menu'))
      ->with('username', $user->name)
      ->with('active', ['myseeds' => true]);
  }

  public function getMessages()
  {
    $user = \Auth::user();

    $userMessages = $user->lastMessages(10)->toArray();
      //->get()->sortByDesc('created_at')->chunk(4)[0]->toArray();
    $unreadmessages = 0;
    foreach($userMessages as &$m) {
      if (($m['sender_id'] != $user->id) && (!$m['read'])){
        $unreadmessages++;
        $m['enabled'] = true;
      }
      if ($m['sender_id'] == $user->id){
        $m['sent'] = true;
      }
      
      //foreach($mgroup as &$m){
      /*  if (isset($m['pivot'])) {
          $t = array();
          if ($m['pivot']['read']){
            $t[1] = true;
            $m['pivot']['read'] = $t;
          
          } else {
            $unreadmessages++;
          }
    }*/
      //}
    }
    return view('seedbank::messages')
      ->with('usermessages', $userMessages)
      ->with('unreadmessages', $unreadmessages)
      ->with('messages', \Lang::get('seedbank::messages'))
      ->with('menu', \Lang::get('seedbank::menu'))
      ->with('username', $user->name)
      ->with('active', ['messages' => true]);
  }

  public function getRegister($id = null)
  {
    $user = \Auth::user();
    $update = false;
    // Authorization
    if($id){
      $seeds_bank= \Caravel\SeedsBank::findOrFail($id);
      if (Gate::denies('update-seeds_bank', $seeds_bank)){
        abort(403);
      }
    }
    //$errors = \Session::get('errors');
    if(\Session::hasOldInput()){
      $info =  \Session::getOldInput();
      if(!empty($errors)){
        \View::share('errors', $errors->default->toArray());
      }
    } 
    if ($id){
      if (! isset($info)) {
        $info = (array)\DB::table('seeds')
          ->join('seeds_banks', 'seeds_banks.seed_id', '=', 'seeds.id')
          ->where('seeds_banks.id', $id)->first();
        $info['months'] = (array)\DB::table('seed_months')->where('seed_id', $info['seed_id'])->lists('month');
        foreach(['variety', 'family', 'species'] as $field){
          $field_a = (array)\DB::table($field)->select('name')->find($info[$field . '_id']);
          $info[$field] = $field_a['name'];
        };
      }
      $info['id'] = $id;
      $update = true;
    }
    //$t = [];
      foreach(['origin', 'polinization', 'direct', 'public', 'available'] as $key){
        if(isset($info[$key])){
          $info[$key] = [$info[$key] => true];
        }
      }

    if(isset($info['months'])){
      $o = array();
      foreach($info['months'] as $i){
        $o[$i] = true;
      }
      $info['months'] = $o;
    }
    if (! isset($info)){
      $info = [];
    }
    return view('seedbank::registerseed', ['update' => $update])
      ->with('messages', \Lang::get('seedbank::messages'))
      ->with('menu', \Lang::get('seedbank::menu'))
      ->with('username', $user->name)
      ->with('active', ['myseeds' => true])
      ->with('oldInput', $info); 
  }

  public function postRegister(Request $request)
  {
    // if error with form
    $this->validate($request, [
      'common_name' => 'required',
      'origin' => 'required',
    ]);
    $seeds_bank_keys = ['quantity','origin','year', 'local', 'description', 'public', 'available', 'description'];
    $seeds_keys = ['sci_name','common_name','polinization','direct', 'description'];
    $seeds_taxonomy = ['species', 'variety','family'];
    $seeds_bank_new = [];
    $seeds_new = [];
    $months_new = [];
    foreach ( $request->input() as $key =>  $value ){
      if (in_array($key, $seeds_keys)){
        $seeds_new[$key] = $value;
      }
      if (in_array($key, $seeds_bank_keys)){
        $seeds_bank_new[$key] = $value;
      }
      if (in_array($key, $seeds_taxonomy)){
        $t = (array)\DB::table($key)->where('name', $value)->first();
        if (! $t) {
          $t['id'] = \DB::table($key)->insertGetId(['name' => $value]);
        }
        $seeds_new[$key . '_id'] = $t['id'];
      }
      if ($key == 'months'){
        $months_new = $value;
      }

    }

    $user = \Auth::user();
    $seeds_bank_new['user_id'] = $user->id;

    if ($request->input('_id')){
      $seed_id = $request->input('seed_id');
      \DB::table('seeds_banks')->where('id', $request->input('_id'))
        ->update($seeds_bank_new);
      \DB::table('seeds')->where('id', $seed_id)
        ->update($seeds_new);
      if (! $months_new){
        \DB::table('seed_months')->where('seed_id', $seed_id)->delete();
      } else {
        $months = \DB::table('seed_months')->where('seed_id', $seed_id)->lists('month');
        \DB::table('seed_months')->where('seed_id', $seed_id)
          ->whereNotIn('month', $months_new)->delete();
        foreach($months_new as $month){
          if (! in_array($month, $months)){
            \DB::table('seed_months')
              ->insert(['seed_id' => $seed_id, 'month'=> $month ]);
          }
        }
      }
      // maybe flash an 'Updated $id' message
      //dd('has has ID');
    } else {
      $seeds_bank_new['available'] = true;
      $seed_id = \DB::table('seeds')
        ->insertGetId($seeds_new);
      $seeds_bank_new['seed_id'] = $seed_id;
      \DB::table('seeds_banks')
        ->insert($seeds_bank_new);
      foreach($months_new as $month){
        \DB::table('seed_months')
          ->insert(['seed_id' => $seed_id, 'month'=> $month ]);
      }
      // maybe flash an 'Added new seed' message
    }

    return redirect('/seedbank');
  }
  public function getPreferences()
  {
    $user = \Auth::user();
    return view('seedbank::preferences')
      ->with('messages', \Lang::get('seedbank::messages'))
      ->with('menu', \Lang::get('seedbank::menu'))
      ->with('username', $user->name)
      ->with('active', ['profile' => true]);
  }
  public function postpreferences()
  {
    //
  }
  public function getSearch()
  {
    $user = \Auth::user();
    return view('seedbank::search')
      ->with('messages', \Lang::get('seedbank::messages'))
      ->with('menu', \Lang::get('seedbank::menu'))
      ->with('username', $user->name)
      ->with('active', ['search' => true]);

  }
  public function postSearch(Request $request)
  {
    // $user = \Auth::user();
    $user = $request->user();
    $q = [];
    foreach($request->input() as $key => $value){
      if (in_array($key, ['common_name', 'sci_name']) && ($value)){
        $q[$key] = $value;
      }
    }
    if (! $q){ return [];}
    //dd($q);
    $query = \DB::table('seeds');
    foreach($q as $key => $value){
      $query->orWhere($key, 'like', '%' . $value . '%');
    }
    $results = $query->select('id', 'common_name', 'sci_name')->distinct()->get();
    /*$result = [];
    foreach($results as $i){
      $myarray = (array)$i;
      $result[] = $myarray;
    };*/
    return $results;
  }
  public function postAutocomplete(Request $request)
  {
    // $user = \Auth::user();
    $user = $request->user();
    $query_term = $request->input('query');
    $query_name = $request->input('query_name');
    if (! in_array($query_name, ['common_name', 'sci_name'])){
      return [];
    }
    $results = \DB::table('seeds')
      ->join('seeds_banks', 'seeds_banks.seed_id', '=', 'seeds.id')
      ->where($query_name, 'like', '%' . $query_term . '%')
      ->where(function($query) use ($user) { $query->where('public', true)->orWhere('user_id', $user->id);})
      ->select('seed_id', $query_name)->distinct()
      ->get();
    $result = array();
    foreach($results as $i){
      $myarray = (array)$i;
      $myarray['value'] = $myarray[$query_name];
      $myarray['id'] = $myarray['seed_id'];

      $result[] = $myarray;
    };
    return $result;
  }
  /*public function postSeed(Request $request, $id = null)
  {
    $seed = \Caravel\Seed::find($id);
    return $seed;
  }*/

  public function postMessageReply(Request $request)
  {
    // if error with form
    $this->validate($request, [
      'body' => 'required',
      'message_id' => 'required',
    ]);
    $message_id = $request->input('message_id');
    $message = $request->user()->messages()->where('id', $message_id)->first();
    if (Gate::denies('reply-message', $message)){
      abort(403);
    }
    $reply = $message->reply(['body' => $request->input('body')]);
    $message->pivot->replied = true;
    $message->pivot->save();

    if ($reply){
      return ["response" => "Message sent"];
    }
    return false;



    // maybe flash an 'Added new seed' message
    //return redirect('/seedbank');
  }

  public function postMessageSend(Request $request)
  {
    // if error with form
    $this->validate($request, [
      'body' => 'required',
      'seed_id' => 'required',
    ]);
    $subject = $request->input('subject');
    $body = $request->input('body');
    $seed_id = $request->input('seed_id');
    $seed = \Caravel\Seed::findOrFail($seed_id);
    $user_ids = \Caravel\SeedsBank::where('seed_id', $seed_id)
      ->where('public', true)
      ->get(['user_id'])
      ->map(function($item, $key)
      {
        return $item['user_id'];
      })->all();
    if (!$user_ids)
    {
      abort(403);
    }
    if (!$subject)
    {
      $subject = $seed->common_name;
    }
    $message = \Caravel\Message::create([
        'user_id' => $request->user()->id,
        'subject' => $subject,
        'body' => $body,
      ]);
    $message->save();
    $message->root_message_id = $message->id;
    $message->save();
    $message->users()->attach($user_ids);
    foreach($user_ids as $u_id)
    {
      $request->user()->startTransaction(['asked_to'=>$u_id, 'seed_id'=>$seed_id]);
    }
    //return ["response" => "Message sent"];

    // maybe flash an 'Added new seed' message
    return redirect('/seedbank/search');
  }
}
