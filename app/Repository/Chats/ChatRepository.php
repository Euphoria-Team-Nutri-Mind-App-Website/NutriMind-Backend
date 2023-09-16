<?php
namespace App\Repository\Chats;

use App\Models\Chat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Interfaces\Chats\ChatRepositoryInterface;

class ChatRepository implements ChatRepositoryInterface
{
    public function create(Request $request){

        $receiver_name = $request->receiver_name;
        $sender_name = Auth::user()->name;

        $chat_id = Chat::where('receiver_name', $receiver_name)->where('sender_name',$sender_name)->first();

        // Check if chat already exists
        if ($chat_id) {
            return response([
                'message' => '$message',
            ]);
        } else {
            $chat = Chat::create([
                'sender_name' => $sender_name,
                'receiver_name' => $receiver_name,
                'last_seen' => null,
            ]);
        }



    }
}
