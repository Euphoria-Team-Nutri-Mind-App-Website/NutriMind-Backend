<?php
namespace App\Repository\Chats;

use App\Events\SendMessage;
use App\Models\Chat;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\MessageRequest;
use App\Interfaces\Chats\ChatRepositoryInterface;

class ChatRepository implements ChatRepositoryInterface
{
    public function create(MessageRequest $request){

        $chat_id=Chat::where('receiver_name',$request->receiver_name)->first();

        //check if chat already exist then we don't need to create one
        if ( isset($chat_id)) {
             //start creatend new message
             $message =  Message::create([
                'chat_id' => $chat_id->id,
                'sender_name' => Auth::user()->name,
                'receiver_name' => $request->receiver_name,
                'content' => $request->content,
                'status' => null,
            ]);

            return $message;


        }else{
            //create chat instance
            $chat = Chat::create([
                'sender_name' => Auth::user()->name,
                'receiver_name' => $request->receiver_name,
                'last_seen' => null,
                ]);

            $message = Message::create([
                'chat_id' => $chat->id,
                'sender_name' => Auth::user()->name,
                'receiver_name' => $request->receiver_name,
                'content' => $request->content,
                'status' => null,
            ]);

            //broadcast(new SendMessage($chat))->toOthers();

            return $message;
        }



    }
}
