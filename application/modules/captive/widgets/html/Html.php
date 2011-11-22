<?php

class Widget_Html extends Unwired_Widget_Abstract
{
    public function render($content)
    {
        if ($this->getView()->splashPage->isMobile()) {
            return $content->getContent();
        }
        return '<h1>'.$content->getTitle().'</h1>' . $content->getContent();
    }

    public function renderAdmin($content, $params = array())
    {
        $this->getView()->assign($params);

        $this->getView()->content = $content;

        return $this->getView()->render('admin.phtml');
    }
}