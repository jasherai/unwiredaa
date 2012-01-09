<?php

class Widget_Iframe extends Unwired_Widget_Abstract
{
    protected $_defaults = array('src' => '',
    							 'height' => 300,
                                 'width' => '100%');

    public function render($content)
    {
        $data = unserialize($content->getContent());

        if (!$data || !is_array($data) || !isset($data['src'])) {
            return '';
        }

        $data = array_merge($this->_defaults, $data);

        $viewPath = $this->getBasePath();

        if ($this->getView()->splashPage->isMobile()) {
            $viewScript = 'iframe-mobile.phtml';
        } else {
            $viewScript = 'iframe.phtml';
        }

        $result = '<iframe';

        foreach ($data as $attrib => $value) {
            $result .= " {$attrib}=\"" . $this->getView()->escape($value) . '"';
        }

        $result .= '></iframe>';

        if (!file_exists("{$viewPath}/views/scripts/{$viewScript}")) {
            return $result;
        }

        return $this->getView()->partial($viewScript, array('iframe' => $result, 'data' => $data));
    }

    public function renderAdmin($content, $params = array())
    {
        $data = @unserialize($content->getContent());

        if (!$data || !is_array($data)) {
            $data = array('desktop' => array('src' => '', 'width' => '100%', 'height' => '300'),
                          'mobile' => array('src' => '', 'width' => '100%', 'height' => '300'));
        }

        if (!isset($data['desktop'])) {
            $data = array('desktop' => $data, 'mobile' => $data);
        }

        $data['desktop'] = array_merge($this->_defaults, $data['desktop']);
        $data['mobile'] = array_merge($this->_defaults, $data['mobile']);

        $this->getView()->iframeData = $data;
        $this->getView()->content = $content;
        $this->getView()->assign($params);

        return $this->getView()->render('admin.phtml');
    }
}