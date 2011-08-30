<?php

class Nodes_Model_Mapper_NodePaginator extends Unwired_Paginator_Adapter_Mapper
{
	protected $_groups = null;

	public function __construct($mapper, $select = null, $groups = null)
	{
		$this->_groups = null;

		parent::__construct($mapper, $select);
	}

	public function setGroups($groups)
	{
		$this->_groups = $groups;
		return $this;
	}

	public function getGroups()
	{
		return $this->_groups;
	}

	public function getItems($offset, $itemCountPerPage)
	{
		$items = parent::getItems($offset, $itemCountPerPage);

		if (null === $this->_groups) {
			return $items;
		}

		$groups = $this->_groups;

		foreach ($items as $item) {
			foreach ($groups as $group) {
				if ($group->getGroupId() == $item->getGroupId()) {
					$item->setGroup($group);
					break;
				}
				$iterator = new RecursiveIteratorIterator($group, RecursiveIteratorIterator::SELF_FIRST);

				foreach ($iterator as $child) {
					if ($item->getGroupId() == $child->getGroupId()) {
						$item->setGroup($child);
						break;
					}
				}
			}
		}

		return $items;
	}
}