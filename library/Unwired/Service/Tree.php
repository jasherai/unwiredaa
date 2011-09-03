<?php

class Unwired_Service_Tree
{

	protected $_defaultMapper = null;

	public function fetchTree()
	{
		$node = $this->_getDefaultMapper()->getEmptyModel();

		$topLevel = $this->_getDefaultMapper()->findBy(array('parent_id' => null));

		foreach ($topLevel as $top) {
			$this->loadTree($top);
			$node->addChild($top);
		}

		return $node;
	}

	public function findNode($node, $parents = false, $children = false)
	{
		if (!$node instanceof Unwired_Model_Tree) {
			$node = $this->_getDefaultMapper()->find($node);
		}

		if (!$node) {
			return false;
		}

		if ($parents) {
			$node->setParent($this->getNodeParent($node, true));
		}

		if ($children) {
			$node->setChildren($this->getNodeChildren($node, true));
		}

		return $node;
	}

	/**
	 * Load both parent and child nodes
	 *
	 * @param Unwired_Model_Tree $node
	 * @return Unwired_Model_Tree
	 */
	public function loadTree(Unwired_Model_Tree $node)
	{
		$this->getNodeParent($node);
		// $node->setParent($this->getNodeParent($node));
		$node->setChildren($this->getNodeChildren($node));

		return $this;
	}

	/**
	 * Get node parent(s)
	 *
	 * @param Unwired_Model_Tree $node
	 * @param bool $recursive
	 * @return Unwired_Model_Tree
	 */
	public function getNodeParent(Unwired_Model_Tree $node, $recursive = true)
	{
		if (null === $node->getParentId()) {
			return null;
		}

		$parent = $this->_getDefaultMapper()->find($node->getParentId());

		$parent->addChild($node);

		if ($recursive) {
			$current = $parent;

			while ($current->getParentId()) {
				$prev = $current;
				$current = $this->_getDefaultMapper()->find($prev->getParentId());
				$current->addChild($prev);
				$prev->setParent($current);
			}
		}

		return $parent;
	}

	/**
	 * Get node children
	 *
	 * @param Unwired_Model_Tree $node
	 * @param bool $recursive
	 * @return array
	 */
	public function getNodeChildren(Unwired_Model_Tree $node = null, $recursive = true)
	{
		if (null == $node) {
			$children = $this->_getDefaultMapper()->findBy(array('parent_id' => null));
		} else {
			$children = $this->_getDefaultMapper()->findBy(array('parent_id' => $node->getTreeBranchId()));
		}

		if ($recursive) {
			foreach ($children as $child) {
				$child->setChildren($this->getNodeChildren($child, $recursive));
			}
		}

		return $children;
	}

	/**
	 * Get (or guess) the default mapper used by this controller
	 * @return Unwired_Model_Mapper
	 */
	protected function _getDefaultMapper()
	{
		if (null === $this->_defaultMapper) {
			preg_match('/^(.*)_Service_([a-z0-0]+)$/i', get_class($this), $match);

			$mapperClass = $match[1] . '_Model_Mapper_' . $match[2];
			$this->_defaultMapper = new $mapperClass;
		} else if (is_string($this->_defaultMapper)) {
			$this->_defaultMapper = new $this->_defaultMapper;
		}

		return $this->_defaultMapper;
	}
}