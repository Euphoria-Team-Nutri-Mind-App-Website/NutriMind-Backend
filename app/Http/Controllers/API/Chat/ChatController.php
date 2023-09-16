<?php

namespace App\Http\Controllers\API\Chat;

use App\Models\Chat;
use App\Models\Message;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Interfaces\Chats\ChatRepositoryInterface;

class ChatController extends Controller
{

    private $chatRepository;

    public function __construct(ChatRepositoryInterface $chatRepository)
    {
        $this->chatRepository = $chatRepository;
    }

    //start new chat method
    public function create(Request $request)
    {
        return $this->chatRepository->create($request);
    }

    public function sendMessage(Request $request){
        $receiver_name = $request->receiver_name;
        $sender_name = Auth::user()->name;
        $chat_id = Chat::where('receiver_name', $receiver_name)->where('sender_name',$sender_name)->first();

        $message = Message::create([
            'chat_id' => $chat_id->id,
            'sender_name' => $sender_name,
            'receiver_name' => $receiver_name,
            'content' => $request->content,
            'status' => null,
        ]);

        return response([
            'message' => $message
        ]);
    }



    //show all messages in the chat
    public function showMessages(){
        $user_name = Auth::user()->name;

        $chat_messages = Message::where(function ($query) use ($user_name) {
            $query->where('receiver_name', $user_name)
                ->orWhere('sender_name', $user_name);
        })->get();

        return response([
            'status' => true,
            'message' => $chat_messages
        ]);
    }


    //show all chats user have
    public function showChats(){
        // dd(Auth::user()->name);
        $chats = Chat::where('sender_name', Auth::user()->name)->get();
        return response([
            'status' => true,
            'chat'=>$chats
        ]);
    }

    //search for specific chat
    public function search(Request $request){
        $filter = $request->receiver_name;
        $chat = Chat::query()
            ->where('sender_name',Auth::user()->name)
            ->where('receiver_name', 'LIKE', "%{$filter}%")
            ->get();
        return response([
            'status' => true,
            'chat'=>$chat
        ]);
    }

    // public function sendMessageNotification(Request $request) {
    //     $receiver = Chat::where('receiver_name', $request->receiver_name);
    //     Notification::send($receiver,new NewMessage);
    //     // Notification::send($userSchema, new OffersNotification($offerData));
    //     dd('notification');
    // }

}
