<?php

namespace Ebookr\Client\Http\Controllers;

use Ebookr\Client\Models\Room;

class RoomController extends Controller
{
    public function index()
    {
        return view('rooms.index')
            ->with('location', location());
    }
    
    public function show(Room $room)
    {
        return view('rooms.show')
            ->with('room', $room);
    }
    
    public function slug(string $slug)
    {
        $page = Room::where('slug', '=', $slug)->first();
        return $this->show($page);
    }
}
