<?php
/* SeoRedirect Test cases generated on: 2010-10-05 18:10:19 : 1286323699*/
App::import('Model', 'Seo.SeoRedirect');
App::import('Component', 'Email');
Mock::generate('EmailComponent');

class SeoRedirectTest extends CakeTestCase {
	public $fixtures = array(
		'plugin.seo.seo_redirect',
		'plugin.seo.seo_uri',
		'plugin.seo.seo_meta_tag',
		'plugin.seo.seo_title',
		'plugin.seo.seo_status_code',
		'plugin.seo.seo_canonical',
	);

	public function startTest() {
		$this->SeoRedirect = ClassRegistry::init('Seo.SeoRedirect');
		$this->SeoRedirect->SeoUri->Email = new MockEmailComponent();
	}
	
	public function testIsRegEx(){
	  $this->assertTrue($this->SeoRedirect->isRegEx('#(.*)\?from\=sb\-tracked\:(.*)#i'));
	  $this->assertTrue($this->SeoRedirect->isRegEx('#(.*)#'));
	  $this->assertFalse($this->SeoRedirect->isRegEx('/blah'));
	  $this->assertFalse($this->SeoRedirect->isRegEx('/blah#anchor'));
	}
	
	public function testBeforeSaveShouldSetApproved(){
	  $this->SeoRedirect->data = array(
	    'SeoRedirect' => array(
	      'redirect' => '/',
	      'priority' => '5',
	      'is_active' => 1,
	    ),
	    'SeoUri' => array(
	    	'uri' => '/newuri'
	    )
	  );
	  $this->assertTrue($this->SeoRedirect->saveAll());
	  $result = $this->SeoRedirect->find('last');
	  $this->assertTrue($result['SeoUri']['is_approved']);
	  $this->SeoRedirect->SeoUri->Email->expectNever('send');
	}
	
	public function testBeforeSaveShouldNotSetApprovedOnRegEx(){
	  $this->SeoRedirect->data = array(
	    'SeoRedirect' => array(
	      'redirect' => '/',
	      'priority' => '5',
	      'is_active' => 1,
	    ),
	    'SeoUri' => array(
	    	'uri' => '#(somenewregex)#i',
	    )
	  );
	  $this->assertTrue($this->SeoRedirect->saveAll());
	  $result = $this->SeoRedirect->find('last');
	  $this->assertFalse($result['SeoUri']['is_approved']);
	  $this->SeoRedirect->SeoUri->Email->expectOnce('send');
	}
	
	public function testFindRedirectListByPriority(){
	  $results = $this->SeoRedirect->findRedirectListByPriority();
	  $this->assertEquals(6, count($results));
	}

	public function endTest() {
		unset($this->SeoRedirect);
		ClassRegistry::flush();
	}

}

