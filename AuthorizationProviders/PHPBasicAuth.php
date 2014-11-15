<?php
namespace Quark\AuthorizationProviders;

use Quark\IQuarkAuthorizableModel;
use Quark\IQuarkAuthorizationProvider;
use Quark\Quark;
use Quark\QuarkModel;

class PHPBasicAuth implements IQuarkAuthorizationProvider {
	/**
	 * @var IQuarkAuthorizableModel $_model
	 */
	private static $_model;
	private static $_user;

	/**
	 * @param $request
	 *
	 * @return mixed
	 */
	public function Initialize ($request) {
		if (!isset($request->authorization) && self::$_user == null) {
			self::Error401();
			header('WWW-Authenticate: Basic realm="' . $_SERVER['SERVER_NAME'] . '"');
		}
		else {
			$parts = explode(' ', $request->authorization);
			$user = explode(':', base64_decode($parts[1]));

			self::$_user = self::$_model->RenewSession($this, array(
				'username' => $user[0],
				'password' => $user[1]
			));
		}
	}

	/**
	 * @param $response
	 *
	 * @return mixed
	 */
	public function Trail ($response) { }

	/**
	 * @param IQuarkAuthorizableModel $model
	 *
	 * @return IQuarkAuthorizationProvider
	 */
	public static function Setup (IQuarkAuthorizableModel $model) {
		self::$_model = $model;

		return new PHPBasicAuth();
	}

	/**
	 * @param IQuarkAuthorizableModel $model
	 * @param                         $credentials
	 *
	 * @return bool
	 */
	public static function Login (IQuarkAuthorizableModel $model, $credentials) {
		self::$_user = $model->Authorize(array(
			'username' => $_SERVER['PHP_AUTH_USER'],
			'password' => $_SERVER['PHP_AUTH_PW']
		));

		return self::$_user != null;
	}

	public static function Error401 ($msg = 'Unauthorized') {
		header('HTTP/1.0 401 ' . $msg);
	}

	/**
	 * @return QuarkModel
	 */
	public static function User () {
		return self::$_user;
	}

	/**
	 * @return bool
	 */
	public static function Logout () {
		self::Error401();
		return true;
	}
}