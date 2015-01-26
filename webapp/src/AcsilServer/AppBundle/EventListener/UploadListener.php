<?php
namespace AcsilServer\AppBundle\EventListener;

use Oneup\UploaderBundle\Event\PostPersistEvent
use Symfony\Component\HttpFoundation\Request;
use AcsilServer\APIBundle\Controller\OperationsController;

class UploadListener {
	public function __construct() {
		$this->apiOptCtrl = new OperationsController();
	}

	public function onUpload(PostPersistEvent $event) {
		$request = new Request();
		$folderId = 0; //FIXME: get from GET params
		$this->apiOptCtrl->uploadAction($request);
	}
}
?>
