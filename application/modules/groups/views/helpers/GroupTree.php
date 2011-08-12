<?php


/**
 * GroupTree helper
 *
 * @uses viewHelper Groups_View_Helper
 */
class Groups_View_Helper_GroupTree {

	/**
	 * @var Zend_View_Interface
	 */
	public $view;

	protected $_group = null;

	/**
	 *
	 */
	public function groupTree(Groups_Model_Group $group) {
		$this->_group = $group;
		return $this;
	}

	public function render(Groups_Model_Group $group = null)
	{
		if (null === $group) {
			$group = $this->_group;
		}

		$result = '<ul>';
		$result .= '<li id="group_' . $group->getGroupId() . '"><a href="javascript;">'
				. $group->getName() . '</a>' . $this->_renderChildren($group) . '</li>';
		$result .= '</ul>';

		return $result;
	}

	protected function _renderChildren(Groups_Model_Group $group)
	{
		if (!$group->getChildren()) {
			return '';
		}

		$result = '<ul>';
		foreach ($group->getChildren() as $child) {
			$result .= '<li id="group_' . $child->getGroupId() . '"><a href="javascript:;">'
					. $child->getName() . '</a>' . $this->_renderChildren($child) . '</li>';
		}
		$result .= '</ul>';

		return $result;
	}

	public function __toString()
	{
		return $this->render();
	}

	/**
	 * Sets the view field
	 * @param $view Zend_View_Interface
	 */
	public function setView(Zend_View_Interface $view) {
		$this->view = $view;
	}
}
