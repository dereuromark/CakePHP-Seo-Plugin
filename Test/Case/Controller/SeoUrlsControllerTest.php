<?php
/* SeoUrls Test cases generated on: 2011-11-05 00:46:47 : 1320475607*/
App::uses('SeoUrlsController', 'Seo.Controller');

class TestSeoUrlsController extends SeoUrlsController {
	public $autoRender = false;

	public function redirect($url, $status = null, $exit = true) {
		$this->redirectUrl = $url;
	}
}

class SeoUrlsControllerTest extends CakeTestCase {
	public function startTest() {
		$this->SeoUrls = new TestSeoUrlsController();
		$this->SeoUrls->constructClasses();
	}

	public function endTest() {
		unset($this->SeoUrls);
		ClassRegistry::flush();
	}

}