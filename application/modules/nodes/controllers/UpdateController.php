<?php

class Nodes_UpdateController extends Zend_Controller_Action
{

	public function indexAction()
	{
		$mapper = new Nodes_Model_Mapper_Node();

		$service = new Nodes_Service_Node();

		$nodes = $mapper->fetchAll();

		?>
		<html>
			<head>
				<title>Update all UCIs</title>
			</head>
			<body>
		<?php
		$cnt = 0;
		foreach ($nodes as $node) {
			echo '<p>Updating UCI for ' . $node->getMac() . '... ';
			if ($service->writeUci($node)) {
				echo 'success!';
				$cnt++;
			} else {
				echo 'failed!';
			}
			echo '</p>';

		}
		echo "<p>Generated {$cnt} of " . count($nodes) . "</p>";
		?>
		</body>
		</html>
		<?php
		die();
	}
}