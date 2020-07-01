<?php
/**
 * Created by PhpStorm.
 * User: llang
 * Date: 6/2/15
 * Time: 4:48 PM
 */

namespace YouTubeRSS;


/**
 * Class Video
 *
 * @package Video
 */
class Video {

    /**
     * @var
     */
    public $channel;

    /**
     * @var
     */
    public $vidID;

    /**
     * @var
     */
    public $image;

    /**
     * @var
     */
    public $url;

    /**
     * @var
     */
    public $title;

    /**
     * @var bool|null|string
     */
    public $date;

    /**
     * @var
     */
    public $duration;


    /**
     * @param $channel
     * @param $vidID
     * @param $image
     * @param $url
     * @param $title
     */
    function __construct($channel, $vidID, $image, $url, $title, $duration, $date = NULL) {
        $this->channel  = $channel;
        $this->vidID    = $vidID;
        $this->image    = $image;
        $this->url      = $url;
        $this->title    = $title;
        $this->duration = $duration;
        $this->date     = $date ? $date : $this->fetchDateFromSourcePage();
    }


    public function fetchDateFromSourcePage() {
        $dom = new \DOMDocument();
        $dom->loadHTML(file_get_contents($this->url));
        $xpath     = new \DomXpath($dom);
        $classname = 'watch-time-text';
        $datetag   = $xpath->query(
            "//*[contains(concat(' ', normalize-space(@class), ' '), ' $classname ')]"
        );

        list($junk, $datestr) = explode(' on ', $datetag->item(0)->textContent);

	$datestr = $datestr ? $datestr : 'now';
        $date = date('Y-m-d', strtotime($datestr));

        return $date;
    }


    public function save(\ADOConnection $DB) {
        return $DB->Replace('videos', get_object_vars($this), 'vidID', TRUE);
    }


    public function getChannel(\ADOConnection $DB, $id) {
        $sql     = "SELECT fullName FROM channels WHERE id = $id";
        $channel = $DB->GetRow($sql);

        return $channel['fullName'];
    }


}
