<?php
/**
 * Created by PhpStorm.
 * User: llang
 * Date: 6/10/15
 * Time: 2:05 PM
 */

namespace YouTubeRSS;


class KeyInvalidException extends \Exception {

    // Redefine the exception so message isn't optional
    public function __construct($message, $code = 0, \Exception $previous = null) {
        // some code

        // make sure everything is assigned properly
        parent::__construct($message, $code, $previous);
    }

    // custom string representation of object
    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }

}
