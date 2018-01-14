<?php namespace Modules\Seedbank\Http\Controllers;

use Pingpong\Modules\Routing\Controller;
use Illuminate\Http\Request;
use Gate;

use Caravel\User;
use Carbon\Carbon;
use Cmgmyr\Messenger\Models\Message;
use Cmgmyr\Messenger\Models\Participant;
use Cmgmyr\Messenger\Models\Thread;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;

/*use Validator;
use GeoIp2\Database\Reader;*/


class MessagesController extends Controller {

  /**
   * Show all of the message threads to the user.
   *
   * @return mixed
   */
  public function index(Request $request)
  {
    $user = \Auth::user();
    $userId = $user->id;
    // All threads that user is participating in
    $threads = Thread::forUser($userId)->latest('updated_at')->get();
    $contacts = $user->contacts;
    $part = [ 'messages' => true ];

    $view = view('seedbank::messenger', compact('threads', 'contacts', 'part'))
      ->with('active', ['messages' => true])
      ->with('modal', true)
      ->with('modal_content', ' ')
      ->with('bodyId', 'mainapp');

    $message_id = $request->input('message_id', null);
    if ($message_id) {
      $message = Message::findOrFail($message_id);
      $thread = $message->thread;
      $users = User::whereIn('id', $thread->participantsUserIds($userId))->get();
      $thread->markAsRead($userId);
      $messages = [];
      foreach($thread->messages as $message){
        $message['username'] = \Caravel\User::find($message['user_id'])->name;
        $messages[] = $message;
      }

      $view->with('message_id', $message_id)
        ->with(
          'modal_content',
          view('seedbank::message_show_modal')
            ->with('message_id', $message_id)
            ->with('tmessages', $messages)
  			    ->with('messages', \Lang::get('seedbank::messages'))
            ->with('thread', $thread)
            ->with('users', $users)
            ->with('csrfToken', csrf_token())
            ->render()
        );
    }

    //return view('seedbank::messenger', compact('threads', 'contacts'))
    return $view;
  }

  /**
   * Shows a message thread.
   *
   * @param $id
   * @return mixed
   */
  public function show($id)
  {
    try {
      $thread = Thread::findOrFail($id);
    } catch (ModelNotFoundException $e) {
      Session::flash('error_message', 'The thread with ID: ' . $id . ' was not found.');
      return redirect('messages');
    }
    // show current user in list if not a current participant
    // $users = User::whereNotIn('id', $thread->participantsUserIds())->get();
    // don't show the current user in list
    $userId = Auth::user()->id;
    $messages = [];
    foreach($thread->messages as $message){
      $message['username'] = \Caravel\User::find($message['user_id'])->name;
      $messages[] = $message;
    }

    $users = User::whereIn('id', $thread->participantsUserIds($userId))->get();
    $thread->markAsRead($userId);
    return view('seedbank::modal-show-thread', compact('thread', 'users'))
      ->with('tmessages', $messages);
  }

  /**
   * Creates a new message thread.
   *
   * @return mixed
   */
  public function create()
  {
    $contacts = \Auth::user()->contacts;
    return view('seedbank::modal-new-thread', compact('contacts'));
  }

  /**
   * Stores a new message thread.
   *
   * @return mixed
   */
  public function store()
  {
    $input = Input::all();
    $thread = Thread::create(
      [
        'subject' => $input['subject'],
      ]
    );
    $thread->save();
    // Message
    Message::create(
      [
        'thread_id' => $thread->id,
        'user_id'   => Auth::user()->id,
        'body'      => $input['body'],
      ]
    );
    // Sender
    Participant::create(
      [
        'thread_id' => $thread->id,
        'user_id'   => Auth::user()->id,
        'last_read' => new Carbon,
      ]
    );
    // Recipients
    if (Input::has('recipients')) {
      $thread->addParticipant(explode(";", $input['recipients']));
    }
    return redirect('/messages');
  }

  /**
   * Adds a new message to a current thread.
   *
   * @param $id
   * @return mixed
   */
  public function update($id)
  {
    try {
      $thread = Thread::findOrFail($id);
    } catch (ModelNotFoundException $e) {
      Session::flash('error_message', 'The thread with ID: ' . $id . ' was not found.');
      return redirect('messages');
    }
    $thread->activateAllParticipants();
    // Message
    Message::create(
      [
        'thread_id' => $thread->id,
        'user_id'   => Auth::id(),
        'body'      => Input::get('body'),
      ]
    );
    // Add replier as a participant
    $participant = Participant::firstOrCreate(
      [
        'thread_id' => $thread->id,
        'user_id'   => Auth::user()->id,
      ]
    );
    $participant->last_read = new Carbon;
    $participant->save();
    // Recipients
    if (Input::has('recipients')) {
      $thread->addParticipants(Input::get('recipients'));
    }
    return redirect('/messages');
  }

}
