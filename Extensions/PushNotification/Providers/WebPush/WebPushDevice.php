<?php
namespace Quark\Extensions\PushNotification\Providers\WebPush;

use Quark\Quark;
use Quark\QuarkEncryptionKey;
use Quark\QuarkKeyValuePair;
use Quark\QuarkSQL;
use Quark\QuarkURI;

use Quark\Extensions\Quark\EncryptionAlgorithms\EncryptionAlgorithmEC;

use Quark\Extensions\PushNotification\IQuarkPushNotificationDevice;

use Quark\Extensions\PushNotification\PushNotificationDevice;

/**
 * Class WebPushDevice
 *
 * @package Quark\Extensions\PushNotification\Providers\WebPush
 */
class WebPushDevice implements IQuarkPushNotificationDevice {
	/**
	 * @var string $_endpoint
	 */
	private $_endpoint;

	/**
	 * @var QuarkEncryptionKey $_keyPublic
	 */
	private $_keyPublic;

	/**
	 * @var string $_keyAuth
	 */
	private $_keyAuth;

	/**
	 * @var string $_encoding
	 */
	private $_encoding;

	/**
	 * @var QuarkKeyValuePair $_id
	 */
	private $_id;

	/**
	 * @param string $endpoint = null
	 *
	 * @return string
	 */
	public function Endpoint ($endpoint = null) {
		if (func_num_args() != 0)
			$this->_endpoint = $endpoint;

		return $this->_endpoint;
	}

	/**
	 * @param QuarkEncryptionKey $key = null
	 *
	 * @return QuarkEncryptionKey
	 */
	public function KeyPublic (QuarkEncryptionKey $key = null) {
		if (func_num_args() != 0)
			$this->_keyPublic = $key;

		return $this->_keyPublic;
	}

	/**
	 * @param string $key = null
	 *
	 * @return string
	 */
	public function KeyAuth ($key = null) {
		if (func_num_args() != 0)
			$this->_keyAuth = $key;

		return $this->_keyAuth;
	}

	/**
	 * @param string $encoding = null
	 *
	 * @return string
	 */
	public function Encoding ($encoding = null) {
		if (func_num_args() != 0)
			$this->_encoding = $encoding;

		return $this->_encoding;
	}

	/**
	 * @param QuarkKeyValuePair $id = null
	 *
	 * @return QuarkKeyValuePair
	 */
	public function &ID (QuarkKeyValuePair $id = null) {
		if (func_num_args() != 0)
			$this->_id = $id;

		return $this->_id;
	}

	/**
	 * @return array
	 */
	public function SQL () {
		return array(
			'$like' => QuarkSQL::LikeEscape('%"keys":' . $this->_id->Key() . '%')
		);
	}

	/**
	 * @param string $id = ''
	 *
	 * @return bool
	 */
	public function NeedUpdate ($id = '') {
		$target = json_decode($id);

		if ($target == null) return true;
		if (!self::ValidateID($id)) return true;

		$keys = json_encode($data->keys);
		if ($keys == $this->_id->Value())
			return $data->endpoint != $this->_endpoint;

		return false;
	}

	/**
	 * @param string $id = ''
	 *
	 * @return bool
	 */
	public function IsSame ($id = '') {
		$target = json_decode($id);

		if ($target == null) return false;
		if (!self::ValidateID($id)) return false;

		$keys = json_encode($data->keys);

		return $keys == $this->_id->Value();
	}

	/**
	 * @param string $id = ''
	 *
	 * @return bool
	 */
	public static function ValidateID ($id = '') {
		$data = json_decode($id);

		return isset($data->endpoint) && isset($data->keys->p256dh) && isset($data->keys->auth);
	}

	/**
	 * @param PushNotificationDevice $device
	 *
	 * @return bool
	 */
	public function PushNotificationDeviceValidate (PushNotificationDevice &$device) {
		return self::ValidateID($device->id);
	}

	/**
	 * @param PushNotificationDevice $device
	 *
	 * @return bool
	 */
	public function PushNotificationDeviceFromDevice (PushNotificationDevice $device) {
		$data = json_decode($device->id);

		if (!$data || !isset($data->endpoint) || !isset($data->keys)) {
			Quark::Log('[PushNotification:WebPushDevice] Invalid device "' . print_r($data, true) . '"', Quark::LOG_WARN);
			return false;
		}

		if (!isset($data->keys->p256dh) || !isset($data->keys->auth)) {
			Quark::Log('[PushNotification:WebPushDevice] Can not determine device encryption protocol "' . print_r($data, true) . '"', Quark::LOG_WARN);
			return false;
		}

		$this->Endpoint($data->endpoint);
		$this->KeyPublic(EncryptionAlgorithmEC::SECDecode(QuarkURI::Base64Decode($data->keys->p256dh), EncryptionAlgorithmEC::OPENSSL_CURVE_PRIME256V1));
		$this->KeyAuth($data->keys->auth);
		$this->ID(new QuarkKeyValuePair(json_encode($data->keys), $data->endpoint));

		return true;
	}
}