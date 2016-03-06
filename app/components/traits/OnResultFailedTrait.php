<?php

namespace App\Components;

/**
 * Class OnResultFailedTrait
 * @package App\Components
 * @author Jakub Hadamčík <jakub@hadamcik.cz>
 */
trait OnResultFailedTrait
{
    /** @var array */
    public $onResultFailed = [];

    /**
     * @param \Exception $ex
     */
    public function callOnResultFailed(\Exception $ex)
    {
       foreach($this->onResultFailed as $onResultFailed) {
           $onResultFailed($ex);
       }
    }
}
