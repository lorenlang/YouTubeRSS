<?php
/**
 * Created by PhpStorm.
 * User: llang
 * Date: 6/2/15
 * Time: 4:22 PM
 */

use Carbon\Carbon;
use YouTubeRSS\ChannelCollection;

//use YouTubeRSS\VideoCollection;
use YouTubeRSS\Video;

require_once 'config.php';
require_once 'database.php';


function addVideo($DB, $channel, $vidID, $title, $duration, $date = NULL)
{
    if ( ! $date) {
        $date = Carbon::now();
    }

    $vidUrl = str_replace('[VIDEO_ID]', $vidID, VIDEO_URL);
    $imgUrl = str_replace('[VIDEO_ID]', $vidID, IMAGE_URL);

    $video = new Video($channel, $vidID, $imgUrl, $vidUrl, $title, $duration, $date);

    $ret = $video->save($DB);

    echo "\t" . $title . PHP_EOL;
}


if ( ! (file_exists(STORAGE_PATH) && is_dir(STORAGE_PATH) && is_writable(STORAGE_PATH))) {
    // Set up storage directory if it doesn't exist
    mkdir(STORAGE_PATH);
} else {
    // Otherwise, remove all temp files leftover from last run
    foreach (glob(STORAGE_PATH . '/*.html') as $tempfile) {
        if (is_file($tempfile)) {
            unlink($tempfile);
        }
    }
}


$channels = new ChannelCollection($DB);
$channels->getAll();

//$videos = new VideoCollection($DB);

foreach ($channels->items as $channel) {

    echo PHP_EOL;
    echo PHP_EOL;
    echo $channel->id . ' - ' . $channel->fullName . ':  ' . PHP_EOL;


    // download the main videos page
    $srch = ['[URLTYPE]', '[URLNAME]'];
    $repl = [$channel->urlType, $channel->urlName];

    $url = str_replace($srch, $repl, CHANNEL_URL);

    $filename = STORAGE_PATH . "/$channel->id.html";
    exec("wget $url -O $filename > /dev/null 2>&1");

    if (is_file($filename)) {
        $work = file_get_contents($filename);


        if (strpos($work, 'window["ytInitialData"] =') !== FALSE) {

            [$junk, $work] = explode('window["ytInitialData"] =', $work);
            [$work, $junk] = explode(PHP_EOL, $work);


            $json = json_decode(rtrim(trim($work), ';'));
            $vids = $json->contents->twoColumnBrowseResultsRenderer->tabs[1]->tabRenderer->content->sectionListRenderer->contents[0]->itemSectionRenderer->contents[0]->gridRenderer->items;

            foreach ($vids as $vid) {
                if (isset($vid->gridVideoRenderer)) {

                    $vidID    = $vid->gridVideoRenderer->videoId;
                    $title    = $vid->gridVideoRenderer->title->simpleText ?? $vid->gridVideoRenderer->title->runs[0]->text;
                    $date     = $vid->gridVideoRenderer->publishedTimeText->simpleText;
                    $duration = $vid->gridVideoRenderer->thumbnailOverlays[0]->thumbnailOverlayTimeStatusRenderer->text->simpleText;

                    $now  = Carbon::now();
                    $date = $now->sub(str_replace(['Streamed ', ' ago'], ['', ''], $date));

                    echo "\t" . $title . PHP_EOL;
                    addVideo($DB, $channel->id, $vidID, $title, $duration, $date);
                }
            }

        } else if (strpos($work, 'var ytInitialData = ') !== FALSE) {
            [$junk, $work] = explode('var ytInitialData = ', $work);
            [$work, $junk] = explode(';</script', $work);


            $json = json_decode(rtrim(trim($work), ';'));


            if (isset($json->contents->twoColumnBrowseResultsRenderer->tabs[1]->tabRenderer->content->sectionListRenderer)) {
                $vids = $json->contents->twoColumnBrowseResultsRenderer->tabs[1]->tabRenderer->content->sectionListRenderer->contents[0]->itemSectionRenderer->contents[0]->gridRenderer->items;
                $type = 1;
            } else if (isset($json->contents->twoColumnBrowseResultsRenderer->tabs[1]->tabRenderer->content->richGridRenderer)) {
                $vids = $json->contents->twoColumnBrowseResultsRenderer->tabs[1]->tabRenderer->content->richGridRenderer->contents;
                $type = 2;
            }

            foreach ($vids as $vid) {

                if ($type = 1 && isset($vid->gridVideoRenderer)) {
                    $vidRenderer = $vid->gridVideoRenderer;
                } else if ($type = 1 && isset($vid->richItemRenderer->content->videoRenderer)) {
                    $vidRenderer = $vid->richItemRenderer->content->videoRenderer;
                }

                $vidID    = $vidRenderer->videoId;
                $title    = $vidRenderer->title->simpleText ?? $vidRenderer->title->runs[0]->text;
                $date     = $vidRenderer->publishedTimeText->simpleText;
                $duration = $vidRenderer->thumbnailOverlays[0]->thumbnailOverlayTimeStatusRenderer->text->simpleText;

                $now  = Carbon::now();
                $date = $now->sub(str_replace(['Streamed ', ' ago'], ['', ''], $date));

                // echo "\t" . $title . PHP_EOL;
                addVideo($DB, $channel->id, $vidID, $title, $duration, $date);
            }

        } else {

            $dom = new DOMDocument();
            $dom->loadHTML(file_get_contents($filename));

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

                // Extract the video title
                $title = $vid->getElementsByTagName('h3')
                             ->item(0)
                             ->getElementsByTagName('a')
                             ->item(0)
                             ->getAttribute('title');

                $title = mb_convert_encoding($title, "ISO-8859-1", mb_detect_encoding($title, "auto", TRUE));


                $duration = '';
                foreach ($vid->getElementsByTagName('span') as $span) {
                    if ($span->hasAttribute('class') && $span->getAttribute('class') == 'video-time') {
                        $duration = $span->textContent;
                    }
                }

                // echo "\t" . $title . PHP_EOL;
                addVideo($DB, $channel->id, $vidID, $title, $duration);
            }

        }

    }

}

