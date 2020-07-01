<?php
/**
 * Created by PhpStorm.
 * User: llang
 * Date: 6/11/15
 * Time: 4:06 PM
 */

namespace VideoCollection;


use Collection\Collection;
use Video\Video;

class VideoCollection extends Collection {


    function __construct($DB) {
        parent::__construct($DB);
    }

    public function getAll() {

        $sql    = 'SELECT * FROM videos';
        $videos = $this->DB->GetAll($sql);

        $this->populateCollection($videos);
    }


    public function getDateRange($from, $thru) {

        if ($from > $thru) {
            list($from, $thru) = array($thru, $from);
        }

        $sql    = "SELECT * FROM videos WHERE date >= '$from' AND date <= '$thru'";
        $videos = $this->DB->GetAll($sql);

        $this->populateCollection($videos);
    }

    /**
     * @param $videos
     * @throws \KeyHasUseException\KeyHasUseException
     */
    private function populateCollection($videos) {
        foreach ($videos as $video) {
            $this->addItem(
                new Video(
                    $video['channel'],
                    $video['vidID'],
                    $video['image'],
                    $video['url'],
                    $video['title'],
                    $video['duration'],
                    $video['date']
                )
            );
        }
    }


}
