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

class Captive_View_Helper_WidgetAdmin extends Zend_View_Helper_Abstract
{
    public function widgetAdmin(Captive_Model_Content $content, array $params = array())
    {
        $widget = $content->getWidget();

        if (empty($widget)) {
            $widget = 'Html';
        }
        $widget = 'Widget_' . ucfirst($widget);

        $widget = new $widget;

        return $widget->renderAdmin($content, $params);
    }
}