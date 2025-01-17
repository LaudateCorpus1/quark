<?php
namespace Quark\Extensions\PushNotification\Providers\GoogleGCM;

use Quark\Extensions\PushNotification\IQuarkPushNotificationDevice;
use Quark\Extensions\PushNotification\PushNotificationDevice;

/**
 * Class GoogleGCMDevice
 *
 * @package Quark\Extensions\PushNotification\Providers\GoogleGCM
 */
class GoogleGCMDevice implements IQuarkPushNotificationDevice {
	/**
	 * @param PushNotificationDevice $device
	 *
	 * @return bool
	 */
	public function PushNotificationDeviceFromDevice (PushNotificationDevice $device) {
		// TODO: Implement PushNotificationDeviceFromDevice() method.
	}

	/**
	 * @param PushNotificationDevice $device
	 *
	 * @return bool
	 */
	public function PushNotificationDeviceValidate (PushNotificationDevice &$device) {
		return true;
	}
}