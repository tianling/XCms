<?php
/**
 * @name CmsController.php UTF-8
 * @author ChenJunAn<lancelot1215@gmail.com>
 * 
 * Date 2013-8-8
 * Encoding UTF-8
 */
class CmsController extends CController{
	/**
	 * @var string the default layout for the controller view. Defaults to '//layouts/column1',
	 * meaning using a single column layout. See 'protected/views/layouts/column1.php'.
	 */
	public $layout='//layouts/column1';
	/**
	 * @var array context menu items. This property will be assigned to {@link CMenu::items}.
	 */
	public $menu=array();
	/**
	 * @var array the breadcrumbs of the current page. The value of this property will
	 * be assigned to {@link CBreadcrumbs::links}. Please refer to {@link CBreadcrumbs::links}
	 * for more details on how to specify this property.
	 */
	public $breadcrumbs=array();
	/**
	 * @var CmsApplication
	 */
	public $app;
	/**
	 * @var CHttpRequest
	 */
	public $request;
	/**
	 * when a action is defined as a CAction
	 * then that action's name is his name and this subfix
	 * @var string $actionSubfix used in @method self::actions()
	 */
	public $actionClassSubfix = 'Action';
	/**
	 * @var string $actionClassFolder used in @method self::actions()
	 */
	public $actionClassPathAlias = 'application.controllers';
	
	public function init(){
		$this->app = Yii::app();
		$this->request = Yii::app()->getRequest();
	}
	
	/**
	 * @param string $name
	 * @param mixed $defaultValue
	 * @return mixed
	 */
	public function getPost($name=null,$defaultValue=null){
		if ( $name !== null ){
			return $this->request->getPost($name,$defaultValue);
		}else {
			return $_POST;
		}
	}
	
	/**
	 * @param string $name
	 * @param mixed $defaultValue
	 * @return mixed
	 */
	public function getQuery($name=null,$defaultValue=null){
		if ( $name !== null ){
			return  $this->request->getQuery($name,$defaultValue);
		}else {
			return $_GET;
		}
	}
	
	/**
	 * @param string $name
	 * @param mixed $defaultValue
	 * @return mixed
	 */
	public function getRestParam($name=null,$defaultValue=null){
		$result = $this->request->getRestParams();
		if ( $name !== null ){
			return isset($result[$name]) ? $result[$name] : $defaultValue;
		}
		return $result;
	}
	
	/**
	 * @param string $name
	 * @param mixed $defaultValue
	 * @return mixed
	 */
	public function getRequestParam($name,$defaultValue=null){
		return $this->request->getParam($name,$defaultValue);
	}
	
	protected function response($code=200,$message='',$data=null,$format='json',$contentType='text/html'){
		$status_header = 'HTTP/1.1 '.$code.' '.$this->getStatusCodeMsg($code);
		header($status_header);
		header('Content-type: '.$contentType);
		$response = array(
				'status' => $code,
				'message' => $message,
				'data' => $data
		);
		if ( $format === 'json' ){
			echo json_encode($response);
		}
		Yii::app()->end();
	}
	
	protected function getStatusCodeMsg($code){
		static $codes = Array(
				200 => 'OK',
				400 => 'Bad Request',
				401 => 'Unauthorized',
				402 => 'Payment Required',
				403 => 'Forbidden',
				404 => 'Not Found',
				405 => 'Method Not Allowed',
				500 => 'Internal Server Error',
				501 => 'Not Implemented',
		);
		return (isset($codes[$code])) ? $codes[$code] : '';
	}
	
	/**
	 * @return array
	 */
	public function actions(){
		$actions = $this->getActionClass();
		$folderAlias = "{$this->actionClassPathAlias}.{$this->id}";
	
		foreach( $actions as $name => $option ){
			if ( is_array($option) ){
				$option['class'] = "{$folderAlias}.{$option['class']}{$this->actionClassSubfix}";
				$actions[$name] = $option;
			}else{
				$actions[$option] = "{$folderAlias}.{$option}{$this->actionClassSubfix}";
			}
		}
	
		return $actions;
	}
	
	/**
	 * @return array
	 */
	public function getActionClass(){
		return array();
	}
}