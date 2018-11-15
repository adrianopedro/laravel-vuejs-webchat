<?php

namespace App\Http\Controllers\Chat;

use App\Models\Chat\Chat;
use App\Models\Chat\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\Users\User;

class ChatController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Chat\Chat  $chat
     * @return \Illuminate\Http\Response
     */
    public function show(Chat $chat)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Chat\Chat  $chat
     * @return \Illuminate\Http\Response
     */
    public function edit(Chat $chat)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Chat\Chat  $chat
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Chat $chat)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Chat\Chat  $chat
     * @return \Illuminate\Http\Response
     */
    public function destroy(Chat $chat)
    {
        //
    }


    public function heartbeat(Request $request){
        $chatboxes = Chat::with('messages','users')->get();        
        $chatboxes = $chatboxes->filter(function($chatbox){
            return $chatbox->users()->get()->contains(Auth::user()) ;
        });

        foreach($chatboxes as $id => $chatbox){
            $chatboxes[$id]->messagelist = $chatbox->messages->unique();
        }

        return $chatboxes;
    }

    public function send(Request $request){
        $chat       = Chat::find($request->chatid); 
        $message    = New Message;

        $message->fill([
            "user_id"   => Auth::user()->id,
            "message"   => $request->message,
        ]);
        $message->save();  

        foreach($chat->users->unique() as $user){
            $chat->messages()->attach($message,['user_id' => $user->id]);            
        }

        return $message->load('chat','user');
    }

    public function openchat(Request $request){
        $chatboxes = Chat::with('messages','users')->get();        
        $chatboxes = $chatboxes->filter(function($chatbox){
            return $chatbox->users()->get()->contains(Auth::user()) ;
        });

        $exists  = [];
        foreach($chatboxes as $chat){
            $users      = $chat->users()->pluck('id');
            $users[]    = Auth::user()->id;
            $exists[]   = count(array_intersect($request->users, $users)) == count($request->users) ? $chat : false;
        }

        if(count($exists) == 0){

        }
        // dd($exists);
    }

    public function received(Request $request){
        $chatbox = Chat::find($request->chatid);        
        $chatbox->messages()->wherePivot('user_id',Auth::user()->id)->wherePivot('seen_at',null)->update(['seen_at' => date('Y-m-d H:i:s')]);            
        return $chatbox;
    }































    public function old_heartbeat(Request $request)
    {
    $items = [];
    $chats = Chat::where('to',Auth::user()->id)->where('recd',0)->whereNotNull('from')->groupBy('from')->orderby('id','ASC')->get();

    foreach($chats as $chat) {
        if (!$request->session()->get('openChatBoxes') && $request->session()->get('chatHistory.'.$chat->from)) {
            $items = $request->session()->get('chatHistory.'.$chat->from);
        }

        $items[] = [
            's'     => 0,
            'f'     => $chat->from,
            'fn'    => User::find($chat->from)->name,
            'm'     => $chat->message,
        ];

        if (!$request->session()->get('chatHistory.'.$chat->from)) {            
            $request->session()->put('chatHistory.'.$chat->from ,[]);
        }
        
        $chatHistory    = $request->session()->get('chatHistory.'.$chat->from);
        $chatHistory    = array_merge($chatHistory,[
            's'     => 0,
            'f'     => $chat->from,
            'fn'    => User::find($chat->from)->name,
            'm'     => $chat->message,
        ]);

        $request->session()->put('chatHistory',$chatHistory);
        $request->session()->forget('tsChatBoxes.'.$chat->from);
        $request->session()->put('openChatBoxes.'.$chat->from, $chat->sent);
    }

    if ($request->session()->get('openChatBoxes')) {
        foreach ($request->session()->get('openChatBoxes') as $chatbox => $time) {
            if (!$request->session()->get('tsChatBoxes.'.$chatbox)) {
                $now        = time() - strtotime($time);
                $time       = date('g:iA M dS', strtotime($time));

                $message    = "Sent at $time";
                if ($now > 180) {
                    $items[] = [
                        's'     => 2,
                        'f'     => $chatbox,
                        'fn'    => User::find($chat->from)->name,
                        'm'     => $chat->message,
                        'ts'    => $chat->sent,
                    ];

                    if (!$request->session()->get('chatHistory.'.$chatbox)) {
                        $request->session()->put('chatHistory.'.$chatbox,'');
                    }

                    $request->session()->put('chatHistory.'.$chatbox,[
                        's'     => 2,
                        'f'     => $chatbox,
                        'fn'    => User::find($chat->from)->name,
                        'm'     => $chat->message,
                    ]);
                    $request->session()->put('tsChatBoxes.'.$chatbox,1);                    
                }
            }
        }
    }

    return $items;
    }

    function chatBoxSession(Request $request, $chatbox) {

        $items = $request->session()->get('chatHistory.'.$chatbox) ?? [];

        return $items;
    }

    function startsession(Request $request) {
        $items = [];
        if ($request->session()->get('openChatBoxes')) {
            foreach($request->session()->get('openChatBoxes') as $chatbox => $void) {
                $items[] = $this->chatBoxSession($request, $chatbox);
            }
        }

        return ['user' => Auth::user(), 'items' => $items];
    }

    // function send(Request $request) {

    //     $from           = Auth::user()->id;
    //     $to             = $request->to;
    //     $message        = $request->message;
    //     $userfullname   = User::find($to)->name;

    //     $request->session()->put('openChatBoxes.'.$to,date('Y-m-d H:i:s', time()));

    //     $messages       = $this->sanitize($message);
    //     $request->session()->put('chatHistory.'.$to,$request->session()->get('chatHistory.'.$to) ?? []);

    //     $chatHistory    = $request->session()->get('chatHistory.'.$to);
    //     $chatHistory    = array_merge($chatHistory,[
    //         "s"     => 1,
    //         "f"     => $to,
    //         "fn"    => $userfullname,
    //         "m"     => $message
    //     ]);   
    //     $request->session()->put('chatHistory.'.$to,$chatHistory);
    //     $request->session()->forget('tsChatBoxes.'.$to);
        
    //     $chat = New Chat;
    //     $chat->create([
    //         "from"      => $from,
    //         "to"        => $to,
    //         "message"   => $message,
    //         "sent"      => date("Y-m-d H:i:s"),
    //     ]);

    //     return 'ok';
    // }

    function closeChat() {

        unset($_SESSION['openChatBoxes'][$_POST['chatbox']]);

        echo "1";
        exit(0);
    }

    function sanitize($text) {
        $text = htmlspecialchars($text, ENT_QUOTES);
        $text = str_replace("\n\r", "\n", $text);
        $text = str_replace("\r\n", "\n", $text);
        $text = str_replace("\n", "<br>", $text);
        return $text;
    }
}
