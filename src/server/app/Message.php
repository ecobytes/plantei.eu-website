<?php

namespace Caravel;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{

    protected $fillable = ['subject', 'body', 'user_id', 'reply_to', 'root_message_id'];

    public function users()
    {
        return $this->belongsToMany('Caravel\User', 'message_user');
    }

    /**
     * Get all replies to message.
     *
     * @return HasMany
     */
    public function replies()
    {
        return $this->hasMany('Caravel\Message', 'reply_to');
    }

    /**
     * Reply to a message.
     *
     * @param  array() ['subject'=>false, 'body'=>false]
     * @return Message
     */
    public function reply($params = array())
    {
        $defaults = array(
            'subject' => false,
            'body' => false
        );
        $params = array_merge($defaults, $params);

        if ((! $this->pivot) || (!$params['body']))
        {
            return false;
        }
        $subject = $params['subject'];
        $body = $params['body'];

        if (!$subject)
        {
            $subject = $this->subject;
            if (substr($subject, 0, strlen('RE: ')) !== 'RE: ')
            {
                $subject = "RE: " . $subject;
            }
        }
        $root_message_id = ($this->root_message_id)?$this->root_message_id:$this->id;

        $reply = \Caravel\Message::create([
            'subject' => $subject,
            'body' => $body,
            'user_id' => $this->pivot->user_id,
            'reply_to' => $this->id,
            'root_message_id' => $root_message_id
        ]);
        $reply->save();
        $reply->users()->attach($this->user_id);
        $this->pivot->read = true;
        $this->pivot->replied = true;
        $this->pivot->save();
        return $reply;
    }

    /**
     * Send a message.
     *
     * @param  array() ['subject'=>false, 'body'=>false, 'user_id'=>false, 'recipients' => []]
     * @return Message
     */
    public static function send($params = array())
    {
        $defaults = array(
            'subject' => false,
            'body' => false,
            'user_id'=>false,
            'recipients' => []
        );
        $params = array_merge($defaults, $params);

        if ((! $params['subject']) || (!$params['body']) || (!$params['user_id']) || (!$params['recipients']))
        {
            return false;
        }
        $subject = $params['subject'];
        $body = $params['body'];
        $user_id = $params['user_id'];
        $recipients = $params['recipients'];

        $message = \Caravel\Message::create([
            'subject' => $subject,
            'body' => $body,
            'user_id' => $user_id,
        ]);
        $message->save();
        $message->where('id', $message->id)->update(['root_message_id' => $message->id]);

        $message->users()->attach($recipients);
        return $message;
    }
}
