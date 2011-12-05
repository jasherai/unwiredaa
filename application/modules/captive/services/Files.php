<?php
/**
* Unwired AA GUI
*
* Author & Copyright (c) 2011 Unwired Networks GmbH
* alexander.szlezak@unwired.at
*
* Licensed under the terms of the Affero Gnu Public License version 3
* (AGPLv3 - http://www.gnu.org/licenses/agpl.html) or our proprietory
* license available at http://www.unwired.at/license.html
*/

class Captive_Service_Files
{
    public function getSplashPageFiles($splash)
    {
        return $this->_getFiles($splash, 'splash');
    }

    public function getTemplateFiles($template)
    {
        return $this->_getFiles($template, 'template');
    }

    protected function _getFiles($id, $type = 'splash')
    {
        if (!$id) {
            return array();
        }

        if ($type == 'splash') {
            $splash = $id;

            if (!$splash instanceof Captive_Model_SplashPage) {

                $mapperSplash = new Captive_Model_Mapper_SplashPage();

                $splash = $mapperSplash->find($splash);

                if (!$splash) {
                    return array();
                }
            }

            $id = $splash->getSplashId();

            $path = $this->getSplashPagePath($id);

        } else {

            $template = $id;

            if (!$template instanceof Captive_Model_Template) {

                $mapperTemplate = new Captive_Model_Mapper_Template();

                $template = $mapperTemplate->find($template);

                if (!$template) {
                    return array();
                }
            }

            $id = $template->getTemplateId();

            $path = $this->getTemplatePath($id);
        }


        if (!file_exists($path)) {
            @mkdir($path, 0755, true);
            return array();
        }

        $iterator = new DirectoryIterator($path);

        $files = array();

        foreach ($iterator as $file) {
            if ($file->isDir()) {
                continue;
            }

            $files[] = array('name' => $file->getFilename(),
                             'path' => str_replace(array(PUBLIC_PATH . '/', '\\'), array('', '/'), $file->getPathname()));
        }

        return $files;
    }

    public function copyToSplashpages($files)
    {
        if (!Zend_Registry::isRegistered('splashpages')) {
            return true;
        }

        $splashpages = Zend_Registry::get('splashpages');
        $paths = Zend_Registry::get('shellpaths');

        foreach ($splashpages as $splashpage) {
            foreach ($files as $file) {
                $cmd = "{$paths['scp']} -r ";

                /**
                 * Build ssh/scp command
                 */
                if (isset($splashpage['sshoptions'])) {
                    foreach ($splashpage['sshoptions'] as $switch => $value) {
                        $cmd .= "-{$switch} {$value}";
                    }
                }

                $localPath = "{$file['destination']}/{$file['name']}";

                $cmd .= " {$localPath} ";

                $remotePath = (isset($splashpage['user']) ? " {$splashpage['user']}@" : '')
                              . "{$splashpage['host']}:{$splashpage['publicpath']}/"
                              . str_replace(PUBLIC_PATH, '', $localPath);

                $cmd .= " {$remotePath}";

                exec($cmd, $output, $cmdResult);

                if (APPLICATION_ENV == 'development') {
                    Zend_Debug::dump($cmd, 'cmd');
                    Zend_Debug::dump($output, 'cmd output');
                    Zend_Debug::dump($cmdResult, 'cmd result');
                }
            }
        }
    }

    public function getSplashPagePath($splashId)
    {
        return $this->_getPath() . 'splashpages/' . (int) $splashId . '/upload';
    }

    public function getTemplatePath($templateId)
    {
       return $this->_getPath() . 'templates/' . (int) $templateId . '/upload';
    }

    protected function _getPath($relative = false)
    {
        if ($relative) {
            return 'data/';
        }

        return PUBLIC_PATH . '/data/';
    }
}