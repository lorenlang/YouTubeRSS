<?php
/**
 * Created by PhpStorm.
 * User: llang
 * Date: 6/2/15
 * Time: 4:22 PM
 */

use YouTubeRSS\ChannelCollection;
//use YouTubeRSS\VideoCollection;
use YouTubeRSS\Video;

require_once 'config.php';
require_once 'database.php';


$channels = new ChannelCollection($DB);
$channels->getAll();

//$videos = new VideoCollection($DB);

foreach ($channels->items as $channel) {

    echo PHP_EOL;
    echo PHP_EOL;
    echo $channel->id . ' - ' . $channel->fullName . ':  ' . PHP_EOL;

    //if ($channel->id != 68) { continue; }

    $srch = array('[URLTYPE]', '[URLNAME]');
    $repl = array($channel->urlType, $channel->urlName);

    $url = str_replace($srch, $repl, CHANNEL_URL);
    echo $url . PHP_EOL;

    //if ($channel->id == 68) { $url = '/tmp/neozaz.html'; }

    $dom = new DOMDocument();
    $dom->loadHTML(file_get_contents($url));

    $xpath = new DomXpath($dom);

    $classname = 'channels-content-item';

    $vids = $xpath->query(
        "//*[contains(concat(' ', normalize-space(@class), ' '), ' $classname ')]"
    );


    foreach ($vids as $vid) {

        // Extract video id so we can build image & video urls
        $vidID = $vid->getElementsByTagName('div')
            ->item(0)
            ->getAttribute('data-context-item-id');

        $sql = "select count(*) from videos where vidID = '$vidID'";

//        if (!$DB->GetOne($sql)) {

            // Extract the video title
            $title = $vid->getElementsByTagName('h3')
                ->item(0)
                ->getElementsByTagName('a')
                ->item(0)
                ->getAttribute('title');

            $title = mb_convert_encoding($title, "ISO-8859-1", mb_detect_encoding($title, "auto", true));


            $duration = '';
            foreach ($vid->getElementsByTagName('span') as $span) {
                if($span->hasAttribute('class') && $span->getAttribute('class') =='video-time') {
                    $duration = $span->textContent;
                }
            }


            $vidUrl = str_replace('[VIDEO_ID]', $vidID, VIDEO_URL);
            $imgUrl = str_replace('[VIDEO_ID]', $vidID, IMAGE_URL);

            $video = new Video($channel->id, $vidID, $imgUrl, $vidUrl, $title, $duration);

            $ret = $video->save($DB);

            echo "\t" . $title . PHP_EOL;

    }

}

