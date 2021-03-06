<?php
/**
 * @author Thomas Tanghus, Bart Visscher
 * Copyright (c) 2013 Thomas Tanghus (thomas@tanghus.net)
 * This file is licensed under the Affero General Public License version 3 or
 * later.
 * See the COPYING-README file.
 */

namespace OCA\Contacts;
use OCP\AppFramework\Http\JSONResponse as OriginalResponse,
	OCP\AppFramework\Http\Http;


/**
 * A renderer for JSON calls
 */
class JSONResponse extends OriginalResponse {

	public function __construct($params = array(), $statusCode=Http::STATUS_OK) {
		parent::__construct(array(), $statusCode);
		$this->data['data'] = $params;
	}

	/**
	 * Sets values in the data json array
	 * @param array|object $params an array or object which will be transformed
	 *                             to JSON
	 */
	public function setParams(array $params) {
		$this->setData($params);
		return $this;
		$this->data['data'] = $params;
		$this->data['status'] = 'success';
	}

	public function setData($data){
		$this->data = $data;
		return $this;
	}

	public function setStatus($status) {
		parent::setStatus($status);
		return $this;
	}

	/**
	 * in case we want to render an error message, also logs into the owncloud log
	 * @param string $message the error message
	 */
	public function setErrorMessage($message){
		$this->error = true;
		$this->data = $message;
		return $this;
		//$this->data['status'] = 'error';
	}

	function bailOut($msg, $tracelevel = 1, $debuglevel = \OCP\Util::ERROR) {
		if($msg instanceof \Exception) {
			$msg = $msg->getMessage();
			$this->setStatus($msg->getCode());
		}
		$this->setErrorMessage($msg);
		$this->debug($msg, $tracelevel, $debuglevel);
		return $this;
	}

	function debug($msg, $tracelevel = 0, $debuglevel = \OCP\Util::DEBUG) {
		if(!is_numeric($tracelevel)) {
			return $this;
		}

		if(PHP_VERSION >= "5.4") {
			$call = debug_backtrace(false, $tracelevel + 1);
		} else {
			$call = debug_backtrace(false);
		}

		$call = $call[$tracelevel];
		if($debuglevel !== false) {
			\OCP\Util::writeLog('contacts',
				$call['file'].'. Line: '.$call['line'].': '.$msg,
				$debuglevel);
		}
		return $this;
	}

}