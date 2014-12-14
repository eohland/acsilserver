<?php
namespace Utils;

class BaseController {
  public function listMethods() {
    //TODO: return existing methods only
    return array(
      'get',
      'getAll',
      'create',
      'update',
      'delete',
    );
  }
}
?>
