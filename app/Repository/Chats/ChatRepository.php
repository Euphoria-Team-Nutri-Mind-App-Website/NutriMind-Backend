<?php
namespace App\Repository\Chats;

use App\Models\Chat;
use App\Models\Doctor;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Interfaces\Chats\ChatRepositoryInterface;

class ChatRepository implements ChatRepositoryInterface
{
    public function create($id){

        //$receiver_name = $request->receiver_name;
        $sender_name = Auth::user()->name;
        $receiver_name = Doctor::where('id',$id)->findOrFail('name');
        $chat_id = Chat::where('receiver_name', $receiver_name)->where('sender_name',$sender_name)->first();

        // Check if chat already exists
        if ($chat_id) {
            $user_name = Auth::user()->name;

            $chat_messages = Message::where(function ($query) use ($user_name) {
                $query->where('receiver_name', $user_name)
                    ->orWhere('sender_name', $user_name);
            })->get();

            return response([
                'status' => true,
                'message' => $chat_messages
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
