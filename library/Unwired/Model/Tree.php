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

abstract class Unwired_Model_Tree extends Unwired_Model_Generic implements RecursiveIterator
{
	protected $_children = array();

	protected $_parent = null;

	private $_currentIdx = 0;

	abstract public function getTreeBranchId();
	abstract public function getTreeBranchName();

	/* (non-PHPdoc)
	 * @see RecursiveIterator::hasChildren()
	 */
	public function hasChildren() {
		return (bool) count($this->_children) > 0;
	}

	/* (non-PHPdoc)
	 * @see RecursiveIterator::getChildren()
	 */
	public function getChildren() {
		return $this->current();
	}

	public function setChildren( $children)
	{
		if (null !== $children && !is_array($children)) {
			throw new Unwired_Exception('$children must be null or array');
		}

		$children = (null === $children) ? array() : $children;

		if (is_array($children)) {
			foreach ($children as $child) {
				$this->addChild($child);
			}
		} else {
			$this->_children = $children;
		}

		$this->rewind();

		return $this;
	}

	public function addChild(Unwired_Model_Tree $child) {
		if (!in_array($child, $this->_children)) {
			$child->setParent($this);
			$this->_children[] = $child;
		}

		return $this;
	}

	public function getParent()
	{
		return $this->_parent;
	}

	public function setParent(Unwired_Model_Tree $parent = null)
	{
		$this->_parent = $parent;
		return $this;
	}

	/* (non-PHPdoc)
	 * @see RecursiveIterator::current()
	 */
	public function current() {
		if ($this->valid()) {
			return $this->_children[$this->_currentIdx];
		}

		return null;
	}

	/* (non-PHPdoc)
	 * @see RecursiveIterator::next()
	 */
	public function next() {
		$this->_currentIdx++;

		return $this->current();
	}

	/* (non-PHPdoc)
	 * @see RecursiveIterator::key()
	 */
	public function key() {
		if ($this->valid()) {
			return $this->_currentIdx;
		}
		return null;
	}

	/* (non-PHPdoc)
	 * @see RecursiveIterator::valid()
	 */
	public function valid() {
		if (!$this->hasChildren() || $this->_currentIdx >= count($this->_children)) {
			return false;
		}

		return true;
	}

	/* (non-PHPdoc)
	 * @see RecursiveIterator::rewind()
	 */
	public function rewind() {
		$this->_currentIdx = 0;
	}


}