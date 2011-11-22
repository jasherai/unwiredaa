<?php

class Widget_Links extends Unwired_Widget_Abstract
{
    protected $_config = array('decorate' => true);

    public function render($content)
    {
        $data = unserialize($content->getContent());

        if (!$data || !is_array($data) || !count($data)) {
            return '';
        }

        if (isset($data['links'])) {
            $this->_config = array_merge($this->_config, $data);

            $data = $this->_config['links'];
        }

        $userAgent = Default_Model_UserAgent::getInstance();

        $deviceLinks = array();

        $view = Zend_Layout::getMvcInstance()->getView();

        $templatePath = $view->baseUrl('data/templates/' . $view->splashPage->getTemplateId());

        foreach ($data as $linkProperties) {
            $link = null;

            if (isset($linkProperties[$userAgent->getDevice()])) {
                $link = $linkProperties[$userAgent->getDevice()];
            } else if (isset($linkProperties['other'])) {
                $link = $linkProperties['other'];
            }

            if (!$link) {
                continue;
            }

            $link['content'] = str_replace(':templatePath:', $templatePath, $link['content']);

            $deviceLinks[] = $link;
        }

        if ($this->_config['decorate']) {
            $viewPath = $this->getBasePath();

            if ($this->getView()->splashPage->isMobile()) {
                $viewScript = 'links-mobile.phtml';
            } else {
                $viewScript = 'links.phtml';
            }

            if (file_exists("{$viewPath}/views/scripts/{$viewScript}")) {
                $this->getView()->deviceLinks = $deviceLinks;
                return $this->getView()->render($viewScript);
            }
        }

        $result = '';

        foreach ($deviceLinks as $link) {
            $escape = true;
        	if (preg_match('/\<.*\>/i', $link['content'])) {
        	    $escape = false;
        	}
            $result .= $this->getView()->formHref(array('name' => 'link' . rand(),
                                               			'escape' => $escape,
                                               			'attribs' => $link));
        }

        return $result;
    }

    public function renderAdmin($content, $params = array())
    {
        $this->getView()->assign($params);

        $data = @unserialize($content->getContent());

        if (!$data) {
            $data = array();
        }

        if (!isset($data['links'])) {
            $data = array('links' => $data);
        }

        $data = array_merge($this->_config, $data);

        if (!is_array($data['links']) || empty($data['links'])) {
            $data['links'] = array(array('other' => array('href' => '', 'content' => '')));
        }

        $this->getView()->linksData = $data['links'];
        $this->getView()->linksDecorate = $data['decorate'];

        $this->getView()->content = $content;

        return $this->getView()->render('admin.phtml');
    }
}