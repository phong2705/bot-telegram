<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Http\Controllers\Webhooks;
use App\Models\ChatID;

class TelegramBot implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($input, $data, $all_result, $all_message_chat, $item,  $all_chat_id_in_message,
    $chat_id, $chat_id_info, $new_list_chat_id, $result,
    $new_result, $end_result)
    {
        $this->input = $input;
        $this->data = $data;
        $this->all_result = $all_result;
        $this->all_message_chat = $all_message_chat;
        $this->item = $item;
        $this->all_chat_id_in_message = $all_chat_id_in_message;
        $this->chat_id = $chat_id;
        $this->chat_id_info = $chat_id_info;
        $this->new_list_chat_id = $new_list_chat_id;
        $this->result = $result;
        $this->new_result = $new_result;
        $this->end_result = $end_result;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->input = file_get_contents("https://api.telegram.org/bot5273274128:AAFQL_ffGoRhzUpN2RPv0r2PNRzBR06UVXk/getupdates"); 
        $this->data   = json_decode($this->input); // decoding chat bot info JSON Telegram
        foreach ($this->data->result as $key => $all_result) {
            foreach ($all_result as $subkey => $message_in_result){
                $all_message_chat_info[$subkey][$key] = $message_in_result;
            }
        }
        $this->all_message_chat =  $all_message_chat_info['message'];// get all result message
        $this->all_chat_id_in_message = []; // create new array
        foreach($this->all_message_chat as $item){
            $this->chat_id = $item->chat->id;
            array_push($this->all_chat_id_in_message, $this->chat_id);
        }// get all result chat id in message and push new array
        $this->all_chat_id_in_message = array_unique($this->all_chat_id_in_message);
        $this->chat_id_info = Webhooks::getChatIdDB();
        $this->new_list_chat_id =[]; // create new array
        foreach ($this->chat_id_info as $key => $value){
            array_push($this->new_list_chat_id, $value->chat_id);
        }
        // filter duplicate elements in array
        $this->new_list_chat_id = array_unique($this->new_list_chat_id);
        // compare the elements in the get api array with the get DB array. if different then add to DB
        $this->result=array_diff_assoc($this->all_chat_id_in_message,$this->new_list_chat_id);
        $this->new_result = [];
        foreach ($this->result as $result){
            array_push($this->new_result, $result);
        }// foreach all each elements Chat Id when get data api.telegram
        foreach ($this->new_result as $this->end_result){
            ChatID::create([
                'chat_id' => $this->end_result,
            ]);
        }
    }
}
