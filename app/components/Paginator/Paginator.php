<?php

namespace App\Components\Paginator;

use Nette\Application\UI\Control;
use Ulozenka\Components\FileTemplateTrait;

/**
 * Class Paginator
 * @package App\Components\Paginator
 * @author Jakub Hadamčík <jakub@hadamcik.cz>
 */
class Paginator extends Control
{
    use FileTemplateTrait;

    /**
     * @param \Nette\Utils\Paginator $paginator
     * @param callable $getLinkCallback
     */
    public function render(\Nette\Utils\Paginator $paginator, $getLinkCallback)
    {
        $this->template->maxPage = $paginator->getPageCount();
        $this->template->addFilter('getLink', function ($page) use ($getLinkCallback) {
            return $getLinkCallback($page);
        });
        $this->template->render();
    }
}
