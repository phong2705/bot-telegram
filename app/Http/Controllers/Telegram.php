<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\ChatID;
use Session;
use Illuminate\Support\Facades\DB;

class Telegram extends Controller
{
    
    public function index(){
        return view('telegram.index');
    }

    public function create(){
        $this->index();
        return view('telegram.chatid');
    }
    public function store(Request $request){
        $data = $request->all();
        // Tạo mới user với các dữ liệu tương ứng với dữ liệu được gán trong $data
        ChatID::create($data);
        Session::flash('success', 'create chat id');
        return redirect()->back();
    }
    
}
