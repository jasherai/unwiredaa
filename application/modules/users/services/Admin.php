<?php
class Users_Service_Admin implements Zend_Acl_Assert_Interface
{
	/**
	 * Try to authenticate user
	 *
	 * @param string $username
	 * @param string $password
	 */
	public function login($username, $password)
	{
		$mapper = new Users_Model_Mapper_Admin();

		$user = $mapper->findOneBy(array('email' => $username,
										 'password' => sha1($password)));

		if (!$user) {
			return false;
		}

		/**
		 * Persist user info for logged in user
		 *
		 */
		$auth = Zend_Auth::getInstance();
		$auth->getStorage()->write($user);

		/**
		 * @todo Set up ACL with user
		 */

		return true;
	}

	public function logout()
	{
		Zend_Auth::getInstance()->clearIdentity();

		return true;
	}

	/* (non-PHPdoc)
	 * @see Zend_Acl_Assert_Interface::assert()
	 */
	public function assert(Zend_Acl $acl, Zend_Acl_Role_Interface $role = null, Zend_Acl_Resource_Interface $resource = null, $privilege = null) {
		// TODO Auto-generated method stub

	}

}