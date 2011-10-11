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

class Rest_Service_Auth
{
    protected $_hashAlgoKey = 'sha256';

    protected $_hashAlgoSecret = 'md5';

    protected $_salt = '432g24#@g4G324g23fVfcf234g432';

    public function generateKey(Users_Model_Admin $admin)
    {
        return Zend_Crypt_Hmac::compute('unwiredaa',
                                        $this->_getHashAlgoKey(),
                                        str_replace('@', $this->_salt, $admin->getEmail()));
    }

    public function generateSecret($key, Users_Model_Admin $admin)
    {
        return Zend_Crypt_Hmac::compute($key,
                                        $this->_getHashAlgoSecret(),
                                        str_replace('@', $key, $admin->getEmail()));
    }


    /**
     * Find admin user by REST api key
	 *
     * @param string|Rest_Model_Key $key
     * @return Users_Model_Admin
     */
    public function getAdminByKey($key)
    {
        if (!$key instanceof Rest_Model_Key) {
            $mapperKey = new Rest_Model_Mapper_Key();
            $key = $mapperKey->findOneBy(array('key' => $key,
            								   'active' => 1));
        }

        if (!$key) {
            return null;
        }

        $mapperAdmin = new Users_Model_Mapper_Admin();
        $admin = $mapperAdmin->find($key->getUserId());

        return $admin;
    }

    public function getKeyByAdmin(Users_Model_Admin $admin)
    {
        $mapper = new Rest_Model_Mapper_Key();
        $key = $mapper->findOneBy(array('user_id' => $admin->getUserId(),
                                        'active' => 1));

        return $key;
    }

    /**
     * Check if key is valid
     *
     * @param string|Rest_Model_Key $key
     * @param Unwired_Model_Admin $admin If specified key is checked against
     *        specific admin user
     * @return boolean
     */
    public function checkKey($key, Users_Model_Admin $admin = null)
    {
        if ($key instanceof Rest_Model_Key) {
            $key = $key->getKey();
        }

        if (null === $admin) {
            $admin = $this->getAdminByKey($key);
        }

        return ($this->generateKey($admin) == $key) ? true : false;
    }

    public function checkRequest(Rest_Model_Key $key, Zend_Controller_Request_Abstract $request)
    {
        $params = $request->getParams();

        unset($params['module']);
        unset($params['controller']);
        unset($params['action']);

        if (empty($params['signature'])) {
            return false;
        }

        $signature = $params['signature'];
        unset($params['signature']);

        if (!isset($params['timestamp']) || !$this->_isValidTimestamp($params['timestamp'])) {
            return false;
        }

        $data = http_build_query($params);

        $checkSignature = Zend_Crypt_Hmac::compute($key->getSecret(), $this->_getHashAlgoSecret(), $data);

        return ($checkSignature === $signature) ? true : false;
    }

    protected function _isValidTimestamp($timestamp)
    {
        $serverDate = new Zend_Date();

        $serverTime = $serverDate->getTimestamp() - $serverDate->getGmtOffset();

        $delta = abs($serverTime - $timestamp);

        if ($delta > 360) {
            return false;
        }

        return true;
    }

    protected function _getHashAlgoKey()
    {
        return $this->_hashAlgoKey;
    }

    protected function _getHashAlgoSecret()
    {
        return $this->_hashAlgoSecret;
    }

    protected function _getSalt()
    {
        return $this->_salt;
    }
}