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

/**
 * Instant report form
 * @author B. Krastev <bkrastev@web-teh.net>
 */
class Report_Form_Instant extends Report_Form_Group {

    public function init()
	{
		parent::init();

		$this->removeElement('title');
		$this->removeElement('description');
		$this->removeElement('email');
		$this->removeElement('report_type');
		$this->removeElement('report_interval');
	}

}