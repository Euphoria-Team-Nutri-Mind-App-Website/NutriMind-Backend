<?php
namespace App\Interfaces\Chats;

use Illuminate\Http\Request;
use App\Http\Requests\MessageRequest;


interface ChatRepositoryInterface
{

    // Create new chat method
    public function create( $id);

}
