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

	protected $_exclude = null;

	/**
	 *
	 */
	public function groupTree(Groups_Model_Group $group = null, Groups_Model_Group $exclude = null) {
		$this->_group = $group;
		$this->_exclude = $exclude;
		return $this;
	}

	public function render(Groups_Model_Group $group = null)
	{
		if (null === $group) {
			if ($this->_group == null) {
				return '';
			}

			$group = $this->_group;
		}

		$result = '<ul>';
		$result .= '<li id="group_' . $group->getGroupId() . '"><a href="javascript:;">'
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
			if ($this->_exclude && $child->getGroupId() == $this->_exclude->getGroupId()) {
				continue;
			}
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
