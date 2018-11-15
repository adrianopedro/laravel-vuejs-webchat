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
class Message extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table        = 'chat_messages';
    protected $appends      = ['inout'];
    // public $timestamps      = false;

    /**
     * @var array
     */
    protected $fillable = ['user_id', 'message'];
    protected $dates    = ['created_at', 'updated_at'];

    public function chat(){
        return $this->belongsToMany('\App\Models\Chat\Chat','chat_users_messages')->with('messages');
    }

    public function users(){
        return $this->belongsToMany('\App\Models\Users\User','chat_users_messages');
    }

    public function user(){
        return $this->hasOne("\App\Models\Users\User",'id','user_id');
    }

    public function getInoutAttribute(){
        return $this->user == Auth::user() ? 'out' : 'in';
    }

}
