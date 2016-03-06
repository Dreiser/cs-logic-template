<?php

namespace App\Components;

/**
 * Class OnResultSuccessTrait
 * @package App\Components
 * @author Jakub Hadamčík <jakub@hadamcik.cz>
 */
trait OnResultSuccessTrait
{
    /** @var array */
    public $onResultSuccess = [];

    /**
     * @return void
     */
    public function callOnResultSuccess()
    {
       foreach($this->onResultSuccess as $onResultSuccess) {
           $onResultSuccess();
       }
    }
}
