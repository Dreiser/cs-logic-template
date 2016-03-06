<?php

namespace Ulozenka\Components;

/**
 * Class FileTemplateTrait
 * @package Ulozenka\Components
 * @author Jakub Hadamčík <jakub@hadamcik.cz>
 */
trait FileTemplateTrait
{

    /**
     * Creates component template
     * @param string
     * @return \Nette\Templating\ITemplate
     */
    protected function createTemplate($class = null)
    {
        $template = parent::createTemplate($class);
        // change file extension to .latte
        $template->setFile(preg_replace('/\.[^.]+$/', '.latte', $this->getReflection()->fileName));
        return $template;
    }

    /**
     * Renders component template
     */
    public function render()
    {
        $this->template->render();
    }
}