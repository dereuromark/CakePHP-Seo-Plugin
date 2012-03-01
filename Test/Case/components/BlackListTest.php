<?php
App::uses('Controller', 'Controller');
App::uses('Component', 'Controller');

App::import('Component', 'Seo.BlackList');
App::import('Model', 'Seo.SeoHoneypotVisit');

class BlackListTest extends CakeTestCase {
	public $BlackList = null;
	
	public function startTest(){
		Mock::generate('Controller');
		Mock::generate('SeoHoneypotVisit');
		$this->BlackList = new BlackListComponent();
		$this->BlackList->Controller = new MockController();
		$this->BlackList->SeoBlacklist = new TestBlacklist();
		$this->BlackList->SeoHoneypotVisit = new TestHoneyPotVisit();
	}
	
	public function testIsBannedRedirect(){
		$this->BlackList->Controller->here = '/';
		$this->BlackList->Controller->expectOnce('redirect');
		$this->assertTrue($this->BlackList->__isBanned());
	}
	
	public function testIsBannedOnBannedPage(){
		$this->BlackList->Controller->here = '/seo/seo_blacklists/banned';
		$this->BlackList->Controller->expectNever('redirect');
		$this->assertTrue($this->BlackList->__isBanned());
	}
	
	public function testHandleHoneyPot(){
		$this->BlackList->Controller->here = '/seo/seo_blacklists/honeypot';
		$this->BlackList->Controller->expectOnce('redirect');
		$this->assertTrue($this->BlackList->__isBanned());
	}
	
	public function endTest(){
		unset($this->BlackList);
	}
}

class TestBlacklist extends CakeTestModel {
  public $data = null;
  public $useTable = false;
  
  function isBanned(){
  	return true;
  }
}

class TestHoneyPotVisit extends CakeTestModel {

  public $data = null;
  public $useTable = false;
  
  function isTriggered(){
  	return true;
  }
}