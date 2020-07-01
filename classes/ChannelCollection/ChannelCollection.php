<?php
/**
 * Created by PhpStorm.
 * User: llang
 * Date: 6/11/15
 * Time: 4:06 PM
 */

namespace ChannelCollection;


use Collection\Collection;
use Channel\Channel;

class ChannelCollection extends Collection {



    function __construct(\ADOConnection $DB) {
        parent::__construct($DB);
    }

    public function getAll() {

        $sql = "SELECT * FROM channels WHERE channelType = 'video'";

        $channels = $this->DB->GetAll($sql);

        foreach ($channels as $ch) {
            $this->addItem(new Channel($ch['fullName'], $ch['urlType'], $ch['urlName'], $ch['id']));
        }

    }

}
