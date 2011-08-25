<?php
/**
 * Tree helper - Generate UL from Unwired_Model_Tree for use with jsTree
 *
 * @uses viewHelper Unwired_View_Helper
 */
class Unwired_View_Helper_Tree {

	/**
	 * @var Zend_View_Interface
	 */
	public $view;

	protected $_tree = null;

	protected $_exclude = null;

	protected $_options = array('prefix' => 'node_',
								'class'	 => 'tree');
	/**
	 *
	 */
	public function tree(Unwired_Model_Tree $tree = null, Unwired_Model_Tree $exclude = null, array $options = array())
	{
		$this->_options = array_merge($this->_options, $options);
		$this->_tree = $tree;
		$this->_exclude = $exclude;
		return $this;
	}

	public function render(Unwired_Model_Tree $tree = null)
	{
		if (null === $tree) {
			if ($this->_tree == null) {
				return '';
			}

			$tree = $this->_tree;
		}

		$result = "<ul class=\"{$this->getOption('class')}\">";
		$result .= '<li id="' . $this->getOption('prefix') . $tree->getTreeBranchId() . '"><a href="javascript:;">'
				. $tree->getTreeBranchName() . '</a>' . $this->_renderChildren($tree) . '</li>';
		$result .= '</ul>';

		return $result;
	}

	protected function _renderChildren(Unwired_Model_Tree $node)
	{
		if (!$node->hasChildren()) {
			return '';
		}

		$result = '<ul>';
		foreach ($node as $child) {
			if ($this->_exclude && $child->getTreeBranchId() == $this->_exclude->getTreeBranchId()) {
				continue;
			}
			$result .= '<li id="' . $this->getOption('prefix') . $child->getTreeBranchId() . '"><a href="javascript:;">'
					. $child->getTreeBranchName() . '</a>' . $this->_renderChildren($child) . '</li>';
		}

		$result .= '</ul>';

		return $result;
	}

	public function getOption($key)
	{
		if (!isset($this->_options[$key])) {
			return null;
		}

		return $this->_options[$key];
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
