<?php 
namespace Concrete\Core\Routing;
use Request;
class RedirectResponse extends \Symfony\Component\HttpFoundation\RedirectResponse {

	protected $request;

	public function setRequest(Request $r) {
		$this->request = $r;
	}



}