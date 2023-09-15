<?php
namespace App\Repository\Chats;

use App\Models\Chat;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\MessageRequest;
use App\Interfaces\Chats\ChatRepositoryInterface;

class ChatRepository implements ChatRepositoryInterface
{
    public function create(MessageRequest $request){

        $receiver_name = $request->receiver_name;
        $sender_name = Auth::user()->name;

        $chat_id = Chat::where('receiver_name', $receiver_name)->where('sender_name',$sender_name)->first();

        // Check if chat already exists
        if ($chat_id) {
            $message = Message::create([
                'chat_id' => $chat_id->id,
                'sender_name' => $sender_name,
                'receiver_name' => $receiver_name,
                'content' => $request->content,
                'status' => null,
            ]);

            return response([
                'message' => $message,
            ]);
        } else {
            $chat = Chat::create([
                'sender_name' => $sender_name,
                'receiver_name' => $receiver_name,
                'last_seen' => null,
            ]);

            $message = Message::create([
                'chat_id' => $chat->id,
                'sender_name' => $sender_name,
                'receiver_name' => $receiver_name,
                'content' => $request->content,
                'status' => null,
            ]);

            return response([
                'message' => $message,
            ]);
        }



    }
}
