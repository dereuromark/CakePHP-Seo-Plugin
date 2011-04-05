<?php
class SeoUri extends SeoAppModel {
	var $name = 'SeoUri';
	var $displayField = 'uri';
	var $hasMany = array(
		'SeoMetaTag' => array(
			'className' => 'Seo.SeoMetaTag',
			'foreignKey' => 'seo_uri_id',
			'dependent' => true,
		),
	);
	var $hasOne = array(
		'SeoRedirect' => array(
			'className' => 'Seo.SeoRedirect',
			'foreignKey' => 'seo_uri_id',
			'dependent' => true,
		),
		'SeoTitle' => array(
			'className' => 'Seo.SeoTitle',
			'foreignKey' => 'seo_uri_id',
			'dependant' => true
		),
	);
	
	var $validate = array(
		'uri' => array(
			'unique' => array(
				'rule' => array('isUnique'),
				'message' => 'Must be a unique url'
			),
			'notEmpty' => array(
				'rule' => array('notEmpty'),
				'message' => 'Uri Must be present'
			)
		)
	);
	
	/**
	* Filter fields
	*/
	var $searchFields = array(
		'SeoUri.id','SeoUri.uri'
	);
	
	/**
	* If saving a regular expression, make sure to mark not approved unless
	* is_approved is specifically being sent in.
	* @return true
	*/
	function beforeSave(){
		if(!empty($this->data[$this->alias]['uri']) && $this->isRegEx($this->data[$this->alias]['uri'])){
			if(empty($this->data[$this->alias]['is_approved'])){
				$this->data[$this->alias]['is_approved'] = false;
			}
		}
		else {
			$this->data[$this->alias]['is_approved'] = true;
		}
		return true;
	}
	
	/**
	* Send need approval email if we need it.
	*/
	function afterSave(){
		if(isset($this->data[$this->alias]['is_approved']) && !$this->data[$this->alias]['is_approved']){
			$this->sendNotification(); //Email IT about needing approval... currently me.
		}  
	}
	
	/**
	* Named scope to find for view
	* @param int id
	* @return result of find.
	*/
	function findForViewById($id){
		return $this->find('first', array(
			'conditions' => array('SeoUri.id' => $id),
			'contain' => array('SeoRedirect','SeoTitle','SeoMetaTag')
		));
	}
	
	/**
	* Find the URI id by uri
	* @param string uri
	* @return mixed id
	*/
	function findIdByUri($uri = null){
		return $this->field('id', array("{$this->alias}.uri" => $uri));
	}
	
	/**
	* This is a simple function to return all possible RegEx URIs from the DB
	* (it has to return all of them, since we can't know which it's going to match)
	* So we've wrapped the DB request in a simple cache request, 
	*   configured by setting the config key cacheEngine
	* @return array $uris array(id => uri)
	*/
	function findAllRegexUris() {
		$cacheEngine = SeoUtil::getConfig('cacheEngine');
		if (!empty($cacheEngine)) {
			$cacheKey = 'seo_findallregexuris';
			$uris = Cache::read($cacheKey, $cacheEngine);
		}
		if (!isset($uris) || empty($uris)) {
			$uris = $this->find('all', array(
				'conditions' => array(
					'OR' => array(
						array("{$this->alias}.uri LIKE" => '#%'),
						array("{$this->alias}.uri LIKE" => '%*'),
						),
					"{$this->alias}.is_approved" => true
					),
				'contain' => array(),
				'fields' => array("{$this->alias}.id","{$this->alias}.uri")
				));
			if (!empty($uris) && !empty($cacheEngine)) {
				Cache::write($cacheKey, $uris, $cacheEngine);
			}
		}
		if (!is_array($uris)) {
			return array();
		}
		return $uris;
	}
	
	/**
	* Checks an input $request against regex urls
	* @param string $request
	* @return array $uri_ids array(id)
	*/
	function findRegexUri($request = null) {
		$uri_ids = array();
		$uris = $this->findAllRegexUris();
		foreach($uris as $uri){
			//Wildcard match
			if(strpos($request, str_replace('*','', $uri[$this->alias]['uri'])) !== false){
				$uri_ids[] = $uri[$this->alias]['id'];
			}
			//Regex match
			elseif($this->isRegex($uri[$this->alias]['uri']) && preg_match($uri[$this->alias]['uri'], $request)){
				$uri_ids[] = $uri[$this->alias]['id'];
			}
		}
		return $uri_ids;
	}
	
	
	/**
	* Set as approved
	* @param int id of seo redirect to approve
	* @return boolean result of save
	*/
	function setApproved($id = null){
		if($id) $this->id = $id;
		return $this->saveField('is_approved', true);
	}
	
	/**
	* Send the notification of a regular expression that needs approval.
	* @param int id
	* @return void
	*/
	function sendNotification($id = null){
		if($id) $this->id = $id;
		$this->read();
		
		if(!empty($this->data)){
			if(!isset($this->Email)){
				App::import('Component','Email');
				$this->Email = new EmailComponent();
			}
			$this->Email->to = SeoUtil::getConfig('approverEmail');
			$this->Email->from = SeoUtil::getConfig('replyEmail');
			$this->Email->subject = "301 Redirect: {$this->data[$this->alias]['uri']} to {$this->data[$this->SeoRedirect->alias]['redirect']} needs approval";
			$this->Email->sendAs = 'html';
			$this->Email->send("A new regular expression 301 redirect needs to be approved.<br /><br/>
				
				URI: {$this->data[$this->alias]['uri']}<br />
				REDIRECT: {$this->data[$this->SeoRedirect->alias]['redirect']}<br />
				PRIORITY: {$this->data[$this->SeoRedirect->alias]['priority']}<br /><br />
				
				Link to approve:<br />
				". SeoUtil::getConfig('parentDomain') ."/admin/seo/seo_redirects/approve/{$this->data[$this->SeoRedirect->alias]['id']}<br /><br />
				");
		}
	}
	
}
?>