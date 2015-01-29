<?php
namespace AcsilServer\AppBundle\EventListener;

use Oneup\UploaderBundle\Event\PostPersistEvent;
use Symfony\Component\HttpFoundation\Request;
use AcsilServer\APIBundle\Controller\OperationsController;

class UploadListener {
	public function __construct() {
		$this->apiOptCtrl = new OperationsController();
	}

	public function onUpload(PostPersistEvent $event) {
		echo 'TOTOPLOP42';
	}
}
?>
