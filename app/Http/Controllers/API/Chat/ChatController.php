<?php

namespace App\Http\Controllers\API\Chat;

use App\Models\Chat;
use App\Models\Message;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\MessageRequest;
use Illuminate\Support\Facades\Notification;
use App\Interfaces\Chats\ChatRepositoryInterface;
use App\Notifications\NewMessage;

class ChatController extends Controller
{

    private $chatRepository;

    public function __construct(ChatRepositoryInterface $chatRepository)
    {
        $this->chatRepository = $chatRepository;
    }

    //start new chat method
    public function create(MessageRequest $request)
    {
        return $this->chatRepository->create($request);
    }



    //show all messages in the chat
    public function showMessages(Request $request){
        $chat_messages = Message::all()->where('receiver_name',$request->receiver_name);
        return response([
            'status' => true,
            'message'=> $chat_messages
        ]);
    }


    //show all chats user have
    public function showChats(){
        $chat = Chat::all()->where('sender_name',Auth::user()->name);
        return response([
            'status' => true,
            'chat'=>$chat
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
