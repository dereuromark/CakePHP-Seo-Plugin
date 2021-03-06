<?php
/* SeoUri Test cases generated on: 2011-01-03 10:01:08 : 1294074608*/
App::import('Model', 'Seo.SeoUri');
App::import('Component', 'Email');
Mock::generate('EmailComponent');

class SeoUriTest extends CakeTestCase {
	public $fixtures = array(
		'plugin.seo.seo_uri',
		'plugin.seo.seo_meta_tag',
		'plugin.seo.seo_redirect',
		'plugin.seo.seo_title',
		'plugin.seo.seo_status_code',
		'plugin.seo.seo_canonical',
	);
	
	public function startTest() {
		$this->SeoUri = ClassRegistry::init('Seo.SeoUri');
		$this->SeoUri->Email = new MockEmailComponent();
	}
	
	public function testUrlEncode() {
		$uri = $this->SeoUri->findById(1);
		$this->assertEquals('/blah', $uri['SeoUri']['uri']);
		$this->assertTrue($this->SeoUri->urlEncode(1));
		$result = $this->SeoUri->findById(1);
		$this->assertEquals('/blah', $result['SeoUri']['uri']);
		
		$uri = $this->SeoUri->findById(14);
		$this->assertEquals('/uri with spaces', $uri['SeoUri']['uri']);
		$this->assertTrue($this->SeoUri->urlEncode(14));
		$result = $this->SeoUri->findById(14);
		$this->assertEquals('/uri%20with%20spaces', $result['SeoUri']['uri']);
	}
	
	public function testSetApproved() {
	  $this->SeoUri->id = 6;
	  $this->assertFalse($this->SeoUri->field('is_approved'));
	  $this->SeoUri->setApproved();
	  $this->assertTrue($this->SeoUri->field('is_approved'));
	}
	
	public function testSendNotification() {
	  $this->SeoUri->id = 6;
	  $this->SeoUri->Email->expectOnce('send');
	  $this->SeoUri->sendNotification();
	  $this->assertEquals('301 Redirect: #(.*)#i to / needs approval', $this->SeoUri->Email->subject);
	  $this->assertEquals('html', $this->SeoUri->Email->sendAs);
	}
	
	public function testDeleteUriDeletsMeta() {
		$this->assertTrue($this->SeoUri->SeoMetaTag->hasAny(array('id' => 1)));
		$this->assertTrue($this->SeoUri->SeoMetaTag->hasAny(array('id' => 2)));
		$this->SeoUri->delete(9);
		$this->assertFalse($this->SeoUri->SeoMetaTag->hasAny(array('id' => 1)));
		$this->assertFalse($this->SeoUri->SeoMetaTag->hasAny(array('id' => 2)));
	}
	
	public function testDeleteUriDeleteRedirect() {
		$this->assertTrue($this->SeoUri->SeoRedirect->hasAny(array('id' => 7)));
		$this->SeoUri->delete(7);
		$this->assertFalse($this->SeoUri->SeoRedirect->hasAny(array('id' => 7)));
	}

	public function endTest() {
		unset($this->SeoUri);
		ClassRegistry::flush();
	}

}

