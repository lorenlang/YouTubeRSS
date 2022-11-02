<?php
/**
 * Created by PhpStorm.
 * User: llang
 * Date: 6/2/15
 * Time: 4:22 PM
 */

use YouTubeRSS\VideoCollection;

require_once 'config.php';
require_once 'database.php';


$videos = new VideoCollection($DB);

$thru = date('Y-m-d');
$from = date('Y-m-d', strtotime($thru . '-3 week'));
//$from = date('Y-m-d', strtotime($thru . '-3 year'));

//$videos->getAll();
$videos->getDateRange($from, $thru);


// No sense doing anything if there are no results
if ($videos->length() > 0)
{

    include_once 'vendor/mibe/feedwriter/Item.php';
    include_once 'vendor/mibe/feedwriter/Feed.php';
    include_once 'vendor/mibe/feedwriter/RSS2.php';
    $feed = new FeedWriter\RSS2();

    // Setting some basic channel elements. These three elements are mandatory.
    $feed->setTitle('My YouTube Feeds');
    $feed->setLink(FEED_URL);
    $feed->setDescription('YouTube channels that I follow. Rather grumpy that YouTube got rid of channel RSS feeds.');

    $feed->setImage('YouTube Feeds', FEED_URL, 'http://www.gstatic.com/youtube/img/logo.png');

    $feed->setChannelElement('language', 'en-US');

    $feed->setDate(date(DATE_RSS, time()));
    $feed->setChannelElement('pubDate', date(\DATE_RSS, strtotime('2015-07-15')));

    $feed->addGenerator();



    foreach ($videos->items as $video) {

        // Create a new feed item.
        $newItem = $feed->createNewItem();

        // Add basic elements to the feed item. These three are mandatory for a valid feed.
        $newItem->setTitle($video->title);
        $newItem->setLink($video->url);
        $newItem->setDescription(
            '<div class="link"><a href="' . $video->url . '"><img class="" width="320" height="180" alt="" src="' . $video->image . '"/><p><strong>' . $video->title . '</strong> (' . $video->duration . ')</p></a></div>'
        );

        $newItem->setDate($video->date);
        $newItem->setAuthor($video->getChannel($DB, $video->channel));
        $newItem->setId($video->url, true);

        // Now add the feed item to the main feed.
        $feed->addItem($newItem);
// echo $video->getChannel($DB, $video->channel) . '  ......... ' . $video->title  . PHP_EOL;
    }


    // OK. Everything is done. Now generate the feed.
    $rss = $feed->generateFeed();


    // Do anything you want with the feed in $myFeed. Why not send it to the browser? ;-)
    // You could also save it to a file if you don't want to invoke your script every time.
//    echo $rss;


    // Write the RSS out to the XML file
    $fh = fopen(OUTPUT_FILE, "w") or die("Unable to open file!");
    fwrite($fh, $rss);
    fclose($fh);

}

