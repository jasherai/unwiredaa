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

class Default_Model_NetworkStats extends Unwired_Model_Generic
{
    protected $_devicesOnline = 0;

    protected $_devicesOffline = 0;

    protected $_usersOnline = 0;

    protected $_usersGarden = 0;

    protected $_usersActive = 0;

	/**
     * @return the $_devicesOnline
     */
    public function getDevicesOnline()
    {
        return $this->_devicesOnline;
    }

	/**
     * @param field_type $_devicesOnline
     */
    public function setDevicesOnline($_devicesOnline)
    {
        $this->_devicesOnline = $_devicesOnline;

        return $this;
    }

	/**
     * @return the $_devicesOffline
     */
    public function getDevicesOffline()
    {
        return $this->_devicesOffline;
    }

	/**
     * @param field_type $_devicesOffline
     */
    public function setDevicesOffline($_devicesOffline)
    {
        $this->_devicesOffline = $_devicesOffline;

        return $this;
    }

    public function getDevicesTotal()
    {
        return $this->getDevicesOnline() + $this->getDevicesOffline();
    }


	/**
     * @return the $_usersOnline
     */
    public function getUsersOnline()
    {
        return $this->_usersOnline;
    }

	/**
     * @param field_type $_usersOnline
     */
    public function setUsersOnline($_usersOnline)
    {
        $this->_usersOnline = $_usersOnline;

        return $this;
    }

	/**
     * @return the $_usersGarden
     */
    public function getUsersGarden()
    {
        return $this->_usersGarden;
    }

	/**
     * @param field_type $_usersGarden
     */
    public function setUsersGarden($_usersGarden)
    {
        $this->_usersGarden = $_usersGarden;

        return $this;
    }

	/**
     * @return the $_usersActive
     */
    public function getUsersActive()
    {
        return $this->_usersActive;
    }

	/**
     * @param field_type $_usersActive
     */
    public function setUsersActive($_usersActive)
    {
        $this->_usersActive = $_usersActive;

        return $this;
    }

    public function getUsersTotal()
    {
        return $this->getUsersOnline() + $this->getUsersGarden();
    }

}