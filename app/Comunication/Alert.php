<?php

namespace App\Comunication;

use TelegramBot\Api\BotApi;

class Alert
{
    const TELEGRAM_BOT_TOKEN = '5273274128:AAFQL_ffGoRhzUpN2RPv0r2PNRzBR06UVXk';
    const TELEGRAM_CHAT_ID = 1210419856;

    public static function sendMessage($message)
    {
        $obBotApi = new BotApi(self::TELEGRAM_BOT_TOKEN);

        return $obBotApi->sendMessage(self::TELEGRAM_CHAT_ID, $message);
    }
}