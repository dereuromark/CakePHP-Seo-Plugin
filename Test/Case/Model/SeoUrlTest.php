<?php
/* SeoUrl Test cases generated on: 2011-10-10 16:42:55 : 1318286575*/
App::import('Model', 'Seo.SeoUrl');

class SeoUrlTest extends CakeTestCase {
	public $fixtures = array(
		'plugin.seo.seo_url'
	);
	public function startTest() {
		$this->SeoUrl = ClassRegistry::init('SeoUrl');
	}
	
	public function test_findRedirectByRequest() {
		$this->SeoUrl->settings['cost_add'] = 1;
		$this->SeoUrl->settings['cost_change'] = 1;
		$this->SeoUrl->settings['cost_delete'] = 1;
		$result = $this->SeoUrl->findRedirectByRequest("/some_url");
		$this->assertEquals($result, array('redirect' => '/some', 'shortest' => 4));
		$result = $this->SeoUrl->findRedirectByRequest("/some_other_blah");
		$this->assertEquals($result, array('redirect' => '/some_other_url', 'shortest' => 4));
		$result = $this->SeoUrl->findRedirectByRequest("/some_other");
		$this->assertEquals($result, array('redirect' => '/some_other', 'shortest' => 0));
	}
	
	public function test_levenshtien() {
		$request = "/content/Hearing-loss/Treatment";
		$add = 1;
		$change = 2;
		$delete = 3;
		$lev = levenshtein($request,"/content/Hearing-loss/Treatments", $add, $change, $delete);
		$this->assertEquals(1, $lev);
		
		$lev = levenshtein($request,"/content/articles/Hearing-loss/Protection/30207-Attention-couch-potatoes-time", $add, $change, $delete);
		$this->assertEquals(52, $lev);
	}
	
	public function test_import() {
		$result = $this->SeoUrl->import("/custom-sitemap.xml");
		$this->assertEquals('269', $result);
	}

	public function endTest() {
		unset($this->SeoUrl);
		ClassRegistry::flush();
	}

}