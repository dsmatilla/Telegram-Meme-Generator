<?php
class Telegram {

    private $name;
    private $id;
    private $token;

    public function __construct($name, $id, $token) {
        $this->name = $name;
        $this->id = $id;
        $this->token = $token;
    }

    public function __destruct() {
        unset($this->name);
        unset($this->id);
        unset($this->token);
    }

    private function sendRequest($method, $params) {
        $query = http_build_query($params);
        $c = curl_init();
        curl_setopt($c, CURLOPT_URL, 'https://api.telegram.org/bot'.$this->id.":".$this->token."/".$method."?".$query);
        curl_setopt($c, CURLOPT_RETURNTRANSFER, TRUE);
        $res = curl_exec($c);
        curl_close($c);
        return $res;
    }

    private function sendJsonRequest($method, $params) {
        $query = json_encode($params);
        $headers[] = 'Content-type: application/json';
        $c = curl_init();
        curl_setopt($c, CURLOPT_URL, 'https://api.telegram.org/bot'.$this->id.":".$this->token."/".$method);
        curl_setopt($c, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($c, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($c, CURLOPT_POST, 1);
        curl_setopt($c, CURLOPT_POSTFIELDS, $query);
        $res = curl_exec($c);
        curl_close($c);
        return $res;
    }

    public function sendMessage($to, $text, $keyboard_options = null, $parse_mode = null) {
        $method = "sendMessage";
        $params['chat_id'] = $to;
        $params['text'] = $text;
        $keyboard = new stdClass();
        if($keyboard_options != null) {
            $keyboard->keyboard = array_chunk($keyboard_options, 3);
            $keyboard->resize_keyboard = TRUE;
            $keyboard->one_time_keyboard = TRUE;
        } else {
            $keyboard->remove_keyboard = true;
        }
        $params['reply_markup'] = json_encode($keyboard);
        if($parse_mode != null) {
            $params['parse_mode'] = $parse_mode;
        }
        return $this->sendRequest($method, $params);
    }

    public function forwardMessage($from, $to, $msg_id) {
        $method = "forwardMessage";
        $params['chat_id'] = $to;
        $params['from_chat_id'] = $from;
        $params['message_id'] = $msg_id;
        return $this->sendRequest($method, $params);
    }

    public function getUserPics($id) {
        $method = "getUserProfilePhotos";
        $params['user_id'] = $id;
        $res = $this->sendRequest($method, $params);
        return json_decode($res);
    }

    public function getFile($id) {
        $method = "getFile";
        $params['file_id'] = $id;
        $res = $this->sendRequest($method, $params);
        $res = json_decode($res);
        return file_get_contents('https://api.telegram.org/file/bot'.$this->id.":".$this->token."/".$res->result->file_path);
    }

    public function sendChatAction($chat_id, $action) {
        $method = "sendChatAction";
        $params['chat_id'] = $chat_id;
        $params['action'] = $action;
        $res = $this->sendRequest($method, $params);
        return json_decode($res);
    }

    public function sendSticker($chat_id, $sticker) {
        $method = "sendSticker";
        $params['chat_id'] = $chat_id;
        $params['sticker'] = $sticker;
        $res = $this->sendRequest($method, $params);
        return json_decode($res);
    }

    public function sendPhoto($chat_id, $photo) {
        $method = "sendPhoto";
        $params['chat_id'] = $chat_id;
        $params['photo'] = $photo;
        $res = $this->sendRequest($method, $params);
        return json_decode($res);
    }

    public function sendAudio($chat_id, $audio) {
        $method = "sendAudio";
        $params['chat_id'] = $chat_id;
        $params['audio'] = $audio;
        $res = $this->sendRequest($method, $params);
        return json_decode($res);
    }

    public function sendDocument($chat_id, $document) {
        $method = "sendAudio";
        $params['chat_id'] = $chat_id;
        $params['document'] = $document;
        $res = $this->sendRequest($method, $params);
        return json_decode($res);
    }

    public function sendVideo($chat_id, $video) {
        $method = "sendVideo";
        $params['chat_id'] = $chat_id;
        $params['video'] = $video;
        $res = $this->sendRequest($method, $params);
        return json_decode($res);
    }

    public function sendVoice($chat_id, $voice) {
        $method = "sendVoice";
        $params['chat_id'] = $chat_id;
        $params['voice'] = $voice;
        $res = $this->sendRequest($method, $params);
        return json_decode($res);
    }

    public function sendLocation($chat_id, $latitude, $longitude) {
        $method = "sendLocation";
        $params['chat_id'] = $chat_id;
        $params['latitude'] = $latitude;
        $params['longitude'] = $longitude;
        $res = $this->sendRequest($method, $params);
        return json_decode($res);
    }

    public function answerInlineQueryWithPhotos($chat_id, $objects) {
        $method = "answerInlineQuery";
        $params = new stdClass();
        $params->inline_query_id = $chat_id;
        $params->results = $objects;
        $res = $this->sendJsonRequest($method, $params);
        return json_decode($res);
    }
}