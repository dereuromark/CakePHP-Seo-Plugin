<?php 
App::import('Core', 'ErrorHandler');
App::uses('Controller', 'Controller');

App::import('Model','Seo.SeoRedirect');
App::import('Model','Seo.SeoStatusCode');
App::import('Model','Seo.SeoTitle');
App::import('Model','Seo.SeoUri');
App::import('Model','Seo.SeoUrl');
App::import('Model','Seo.SeoCanonical');
App::import('Model','Seo.SeoMetaTag');

App::uses('SeoAppError', 'Seo.Error');

class AppErrorTest extends CakeTestCase {
  
  public $fixtures = array(
    'plugin.seo.seo_redirect',
    'plugin.seo.seo_uri',
    'plugin.seo.seo_meta_tag',
    'plugin.seo.seo_title',
    'plugin.seo.seo_status_code',
    'plugin.seo.seo_url',
  );
  
  function startTest() {
		$this->AppError = new SeoAppError('ignore', 'ignore', /* test */ true);
		Mock::generate('Controller');
		$this->AppError->controller = new MockController();
	}
	
	public function test_uriToLevenshtein(){
		$_SERVER['REQUEST_URI'] = '/some_url'; // /some is the closest
	  $this->AppError->controller->expectOnce('redirect', array('/some', 301));
	  $this->AppError->__uriToLevenshtein();
	}
	
	public function testUriToRedirectWildCard(){
	  $_SERVER['REQUEST_URI'] = '/blahblahtest'; // /blahblah* will catch this one
	  $this->AppError->controller->expectOnce('redirect', array('/new', 301));
	  $this->AppError->__uriToRedirect();
	}
	
	public function testUriToRedirectWildCardNotMatch(){
		$_SERVER['REQUEST_URI'] = '/admin/blahblahtest'; // /blahblah* should NOT catch this one
	  $this->AppError->controller->expectNever('redirect');
	  $this->AppError->__uriToRedirect();
	}
	
	public function testUriToStatusCodeGone(){
		$_SERVER['REQUEST_URI'] = '/status_gone';
		$result = $this->AppError->__uriToStatusCode(true);
		$this->assertEquals('410', $result);
	}
	
	public function testUriToStatusCodeOk(){
		$_SERVER['REQUEST_URI'] = '/ok_request';
		$result = $this->AppError->__uriToStatusCode(true);
		$this->assertEquals('', $result);
	}
	
	public function testUriToRedirectWithCallbackFull(){
	  $_SERVER['REQUEST_URI'] = '/uri';
	  $this->AppError->controller->expectOnce('redirect', array('/ran_callback', 301));
	  $this->AppError->__uriToRedirect();
	}
	
	public function testUriToRedirectWithRegEx(){
	  $_SERVER['REQUEST_URI'] = '/hearing-aids/558-virginia-beach-virginia-va-23454-virginia-audiology?from=sb-tracked:23457';
	  $this->AppError->controller->expectOnce('redirect', array('/hearing-aids/558-virginia-beach-virginia-va-23454-virginia-audiology', 301));
	  $this->AppError->__uriToRedirect();
	}
	
	public function testUriToRedirectWithRegExTwo(){
	  $_SERVER['REQUEST_URI'] = '/some_url_to?from=sb-tracked:2345';
	  $this->AppError->controller->expectOnce('redirect', array('/some_url_to', 301));
	  $this->AppError->__uriToRedirect();
	}
	
	public function testUriToRedirectWithRegExThree(){
	  $_SERVER['REQUEST_URI'] = '/qas/32074-i-told-hearing-aids';
	  $this->AppError->controller->expectOnce('redirect', array('/questions/32074-i-told-hearing-aids', 301));
	  $this->AppError->__uriToRedirect();
	}
	
	public function testUriToRedirect(){
	  $_SERVER['REQUEST_URI'] = '/blah';
	  $this->AppError->controller->expectOnce('redirect', array('/', 301));
	  $this->AppError->__uriToRedirect();
	}
	
	public function testUriToRedirectNotActive(){
	  $_SERVER['REQUEST_URI'] = '/not_active';
	  $this->AppError->controller->expectNever('redirect');
	  $this->AppError->__uriToRedirect();
	}
	
	public function testPriority(){
	  $_SERVER['REQUEST_URI'] = '/blahblahblah';
	  $this->AppError->controller->expectOnce('redirect', array('/priority', 301));
	  $this->AppError->__uriToRedirect();
	}
	
}

