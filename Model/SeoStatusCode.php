<?php
class SeoStatusCode extends SeoAppModel {

	public $displayField = 'seo_uri_id';
	public $validate = array(
		'seo_uri_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'Must be assigned to a SeoUri',
			),
		),
		'status_code' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'Please enter a status code',
			),
		),
		'priority' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'Priorty must be an integer number',
			),
		),
	);

	public $belongsTo = array(
		'SeoUri' => array(
			'className' => 'Seo.SeoUri',
			'foreignKey' => 'seo_uri_id',
		)
	);
	
	/**
	* Status codes
	*/
	public $codes = array(
		'204' => 'No Content',
		'205' => 'Reset Content',
		'400' => 'Bad Request',
		'401' => 'Unauthorized',
		'402' => 'Payment Required',
		'403' => 'Forbidden',
		'404' => 'Not Found',
		'405' => 'Method Not Allowed',
		'406' => 'Not Acceptable',
		'407' => 'Proxy Authentication Required',
		'408' => 'Request Timeout',
		'409' => 'Conflict',
		'410' => 'Gone',
		'411' => 'Length Required',
		'412' => 'Preconditon Failed',
		'413' => 'Request Entity Too Large',
		'414' => 'Request-URI Too Long',
		'415' => 'Unsupported Media Type',
		'416' => 'Requested Range Not Satisfiable',
		'417' => 'Expectation Failed',
	);
	
	/**
	* Filter fields
	*/
	public $searchFields = array(
		'SeoStatusCode.status_code','SeoStatusCode.id','SeoUri.uri'
	);
	
	/**
	* Check if SEO already exists, if so, unset it and set the ID then save.
	*/
	public function beforeSave(){
		$this->createOrSetUri();
		return true;
	}
	
	public function findCodeList(){
		$retval = array();
		foreach($this->codes as $code => $text){
			$retval[$code] = "$code : $text";
		}
		return $retval;
	}
	
	/**
	* Named scope to find list of uri -> status_codes and order by priority only approved/active
	* @return list of active and approved uri => status_codes ordered by priority
	*/
	public function findStatusCodeListByPriority(){
		return $this->find('all', array(
			'fields' => array("{$this->SeoUri->alias}.uri","{$this->alias}.status_code"),
			'order' => "{$this->alias}.priority ASC",
			'conditions' => array(
				"{$this->alias}.is_active" => true,
				"{$this->SeoUri->alias}.is_approved" => true,
			)
		));
	}
}
