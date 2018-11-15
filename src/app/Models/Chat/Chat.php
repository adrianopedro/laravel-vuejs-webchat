<?php

namespace App\Models\Chat;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

/**
 * @property int $id
 * @property int $from
 * @property int $to
 * @property string $message
 * @property string $sent
 * @property int $recd
 * @property string $created_at
 * @property string $updated_at
 */
class Chat extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'chat';

    /**
     * @var array
     */
    protected $fillable = ['from', 'to', 'message', 'sent', 'recd', 'created_at', 'updated_at'];
    protected $appends  = ['hidden','minimized','newmessages','usersunique'];

    public function messages(){
        return $this->belongsToMany('\App\Models\Chat\Message','chat_users_messages')->withPivot('seen_at','user_id')->with('users','user');        
    }

    public function users(){
        return $this->belongsToMany('\App\Models\Users\User','chat_users_messages')->withPivot('minimized','hidden');
    }

    public function getNewmessagesAttribute(){
        return $this->messages()->wherePivot('user_id',Auth::user()->id)->wherePivot('seen_at',null)->count();
    }

    public function getHiddenAttribute(){    
        return $this->users->contains(Auth::user()) && $this->users->find(Auth::user())->pivot->hidden ? $this->users->find(Auth::user())->pivot->hidden : false;
    }

    public function getMinimizedAttribute(){    
        return $this->users->contains(Auth::user()) &&$this->users->find(Auth::user())->pivot->minimized ? $this->users->find(Auth::user())->pivot->minimized : false;
    }

    public function getUsersuniqueAttribute(){
        return $this->users()->get()->unique();
    }
}
