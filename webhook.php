<?php
    require_once 'settings.php';
    require_once 'lib/telegram.php';

    $content = json_decode(file_get_contents("php://input"));

    $id = $content->inline_query->id;
    $query = $content->inline_query->query;

    if($id) {
        $res = array();
        $x=0;
        foreach (glob("base_pics/*.jpg") as $image) {
            $res[$x] = new stdClass();
            $res[$x]->type = "photo";
            $res[$x]->id = $id.md5($image);
            $res[$x]->photo_url = $config['url']."/index.php?img=".urlencode($image)."&text=".urlencode($query);
            $res[$x]->thumb_url = $config['url']."/".str_replace('base_pics', 'base_thumbs', $image);
            $x++;
        }
        $tg = new Telegram($config['bot_name'], $config['bot_id'], $config['bot_token']);
        $tg->answerInlineQueryWithPhotos($id, $res);
        unset($tg);
    }
