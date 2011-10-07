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

class Default_Service_Logger implements Unwired_Event_Handler_Interface
{
	/* (non-PHPdoc)
	 * @see Unwired_Event_Handler_Interface::handle()
	 */
	public function handle(Unwired_Event_Message $message)
	{
		$data = $message->getData();

		$mapperLog = new Default_Model_Mapper_Log();

		$logEntry = $mapperLog->getEmptyModel();

		$logEntry->setEventId(crc32($message->getMessageId()))
			   	 ->setEventName($message->getMessageId());

		if (isset($data->user)) {
			$logEntry->setUserId($data->user->getUserId());
		}

		if (isset($data->entity)) {
			$entityString = get_class($data->entity);

			$logEntry->setEntity(crc32($entityString));

			if (is_array($data->entityId)) {
				$entityId = current($data->entityId);
			} else {
				$entityId = $data->entityId;
			}

			$logEntry->setEntityId($entityId)
					 ->setEntityName($entityString);
		}

		if (isset($data->params)) {
			$logEntry->setEventData(serialize($data->params));
		}

		$logEntry->setRemoteHost(Zend_Controller_Front::getInstance()->getRequest()->getServer('REMOTE_ADDR'));

		try {
			$mapperLog->save($logEntry);
		} catch (Unwired_Exception $e) {
			die($e->getPrevious()->getMessage());
			// nothing
		} catch (Exception $e) {
			$e = new Unwired_Exception('Unable to log event', 500, $e);
		}

	}
}