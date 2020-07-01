<?php
/**
 * Created by PhpStorm.
 * User: llang
 * Date: 6/2/15
 * Time: 3:56 PM
 */

namespace Channel;


/**
 * Class Channel
 *
 * @package Channel
 */
class Channel {

    /**
     * @var
     */
    public $id;

    /**
     * @var
     */
    public $fullName;

    /**
     * @var
     */
    public $urlType;

    /**
     * @var
     */
    public $urlName;


    /**
     * @param $fullName
     * @param $urlName
     */
    function __construct($fullName, $urlType, $urlName, $id = NULL) {
        $this->fullName = $fullName;
        $this->urlType  = $urlType;
        $this->urlName  = $urlName;
        $this->id       = $id;
    }

}
