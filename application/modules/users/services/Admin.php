<?php
class Users_Model_AdminService
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
		$auth->getStorage()->write($auth);

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
}