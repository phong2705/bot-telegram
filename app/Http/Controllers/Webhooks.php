<?php

namespace App\Http\Controllers;
// use App\Comunication\Alert;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Models\ChatID;
use Exception;
use Illuminate\Support\Facades\DB;
use App\Jobs\TelegramBot;

class Webhooks extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */


    public function gitlabWebhook(Request $request){
        // $this->getPostChatIdAPI();
        $input = $request->getContent(); // get webhook gitlab
        $data = json_decode($input); // encoding JSON Webhooks Gitlab
        $project_info = [
                    'project' =>  $data->project->name,
                    'url_project' =>  $data->project->web_url 
                ]; // get project info to send telegram bot

        date_default_timezone_set("Asia/Ho_Chi_Minh");// change time zone to Asia/Ho_Chi_Minh
        $time_now = date('d/m/Y H:i:s');
        $client = new Client([ "base_uri" => "https://api.telegram.org", ]);
        
        // $chat_id = 1210419856;
        // $chat_id = env('TELEGRAM_CHANNEL_ID');
        $bot_token = env('TELEGRAM_BOT_TOKEN');

        $list_chat_id=[]; // create new array
        $chat_id_info = $this->getChatIdDB();
        foreach ($chat_id_info as $key => $value){
            array_push($list_chat_id, $value->chat_id);
        }// push data form
        foreach (array_unique($list_chat_id) as $chat_id) {
            switch ($data->object_kind) {
                case 'push': //send telegram merge_request event
                    try {
                        $commits = $data->commits;
                        $all_text_commits = $this->printCommits($commits);
                        $text_commits = implode(' ', $all_text_commits);
                        $message = "<b>⥣ NEW PUSH ⥣</b>"."\n".
                                    "Tiều Đề: <a href='".$project_info['url_project']."'>".$project_info['project']."</a>\n".
                                    "Time: ".$time_now."\n".
                                    $text_commits;
                        $response = $client->request("GET", "/bot$bot_token/sendMessage", [
                            "query" => [
                                "chat_id" => $chat_id,
                                "text" => $message,
                                "parse_mode" => "HTML",
                            ]
                        ]);
                        $body = $response->getBody();
                        $arr_body = json_decode($body);
                        if ($arr_body->ok) {
                            echo "Message push event sent!\n";
                        }
                    } catch (Exception $e) {
                        echo $e->getMessage();
                    }
                break;
                case 'merge_request' : //send telegram push event
                    try {
                        $attribute_info = $data->object_attributes;
                        $last_commit_info = $attribute_info->last_commit;

                        $settime = explode("+", $last_commit_info->timestamp);
                        $date = explode("T", $settime[0]);
                        $time_last_commit = $date[0]." ".$date[1];
                        // $time_last_commit = date_format($settime[0], "Y/m/d H:i:s");
                        $message =  "<b>⥷ MERGE REQUEST UPDATE ⥷</b>"."\n".
                                    "Action: <b>".$attribute_info->action."</b>\n".
                                    "Dev: <a >".$data->user->name."</a>\n".
                                    "Title: <a href='".$project_info['url_project']."'>".$project_info['project']."</a>\n".
                                    "<u>Target</u>: <code>".$attribute_info->target_branch."</code>\n".
                                    "<u>Source</u>: <code>".$attribute_info->source_branch."</code>\n".
                                    "Time: ".$time_now."\n".
                                    "============================ \n".
                                    "➾ LASTEST COMMIT ➾ \n".
                                    "ID: <a href='".$last_commit_info->url."'>".$last_commit_info->id."</a>\n".
                                    "Message: ".$last_commit_info->message."\n".
                                    "Time: ".$time_last_commit."\n";
                        $response = $client->request("GET", "/bot$bot_token/sendMessage", [
                            "query" => [
                                "chat_id" => $chat_id,
                                "text" => $message,
                                "parse_mode" => "HTML",
                            ]
                        ]);
                        $body = $response->getBody();
                        $arr_body = json_decode($body);
                        if ($arr_body->ok) {
                            echo "Message merge request event sent!\n";
                        }
                    } catch (Exception $e) {
                        echo $e->getMessage();
                    }
                break;
                default:
                echo 'Không phải push event hoặc merge request event!';
                break;
            }
        }
    }
    
    public function printCommits($commits){
        $list_commits =[];
        foreach( $commits as $key => $commit_infor){
            $key = $key + 1; // amount commits
            $sep_time_zone = explode("+", $commit_infor->timestamp); // separate time zone commits
            $date_time = explode("T", $sep_time_zone[0]); // separate date time commits
            $time_commits = $date_time[0]." ".$date_time[1]; // set time commits
            $text = "============================ \n".
                        "➾ COMMIT ".$key." ➾ \n".
                        "ID: <a href='".$commit_infor->url."'>".$commit_infor->id."</a>\n".
                        "Message: ".$commit_infor->message."\n".
                        "Time: ".$time_commits."\n".
                        "Modified: ".count($commit_infor->modified)."\n".
                        "Remove: ".count($commit_infor->removed)."\n";
            array_push($list_commits, $text);
        }
        return $list_commits; // return list text commits when send to telegram
    }

    // get chat id form DB
    public function getChatIdDB(){
        $value=DB::table('chatinfo')->get('chat_id');
        return $value;
    }
    // Get and Post chat ID in Database when use to cronjob
    // public function getPostChatIdAPI(){
    //     $input = file_get_contents("https://api.telegram.org/bot5273274128:AAFQL_ffGoRhzUpN2RPv0r2PNRzBR06UVXk/getupdates"); 
    //     $data   = json_decode($input); // decoding chat bot info JSON Telegram
    //     foreach ($data->result as $key => $all_result) {
    //         foreach ($all_result as $subkey => $message_in_result){
    //             $all_message_chat_info[$subkey][$key] = $message_in_result;
    //         }
    //     }
    //     $all_message_chat =  $all_message_chat_info['message'];// get all result message
    //     $all_chat_id_in_message = []; // create new array
    //     foreach($all_message_chat as $item){
    //         $chat_id = $item->chat->id;
    //         array_push($all_chat_id_in_message, $chat_id);
    //     }// get all result chat id in message and push new array
    //     $all_chat_id_in_message = array_unique($all_chat_id_in_message);
    //     $chat_id_info = $this->getChatIdDB();
    //     $new_list_chat_id =[]; // create new array
    //     foreach ($chat_id_info as $key => $value){
    //         array_push($new_list_chat_id, $value->chat_id);
    //     }
    //     // filter duplicate elements in array
    //     $new_list_chat_id = array_unique($new_list_chat_id);
    //     // compare the elements in the get api array with the get DB array. if different then add to DB
    //     $result=array_diff_assoc($all_chat_id_in_message,$new_list_chat_id);
    //     $new_result = [];
    //     foreach ($result as $result){
    //         array_push($new_result, $result);
    //     }// foreach all each elements Chat Id when get data api.telegram
    //     foreach ($new_result as $end_result){
    //         ChatID::create([
    //             'chat_id' => $end_result,
    //         ]);
    //     }
    //     $new = new TelegramBot( $input, $data, $all_result, $all_message_chat, $item,  $all_chat_id_in_message,
    //                             $chat_id, $chat_id_info, $new_list_chat_id, $result,
    //                             $new_result, $end_result);
    //     $this->dispatch($new);
    // }
    
}
