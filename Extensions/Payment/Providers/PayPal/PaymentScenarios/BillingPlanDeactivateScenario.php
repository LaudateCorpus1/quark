<?php
namespace Quark\Extensions\Payment\Providers\PayPal\PaymentScenarios;

use Quark\QuarkDTO;

use Quark\Extensions\Payment\IQuarkPaymentInstrument;
use Quark\Extensions\Payment\IQuarkPaymentProvider;
use Quark\Extensions\Payment\IQuarkPaymentScenario;

use Quark\Extensions\Payment\Providers\PayPal\PayPal;

/**
 * Class BillingPlanDeactivateScenario
 *
 * @package Quark\Extensions\Payment\Providers\PayPal\PaymentScenarios
 */
class BillingPlanDeactivateScenario implements IQuarkPaymentScenario {
	/**
	 * @var QuarkDTO $_response
	 */
	private $_response;

	/**
	 * @var string $_id = ''
	 */
	private $_id = '';

	/**
	 * @param string $id = ''
	 */
	public function __construct ($id = '') {
		$this->Id($id);
	}

	/**
	 * @param string $id = ''
	 *
	 * @return string
	 */
	public function Id ($id = '') {
		if (func_num_args() != 0)
			$this->_id = $id;

		return $this->_id;
	}

	/**
	 * @param IQuarkPaymentProvider|PayPal $provider
	 * @param IQuarkPaymentInstrument $instrument = null
	 *
	 * @return bool
	 */
	public function Proceed (IQuarkPaymentProvider $provider, IQuarkPaymentInstrument $instrument = null) {
		$request = array(
			array(
				'path' => '/',
				'value' => array('state' => PayPal::BILLING_STATE_INACTIVE),
				'op' => 'replace'
			)
		);

		$this->_response = $provider->API(
			QuarkDTO::METHOD_PATCH,
			'/v1/payments/billing-plans/' . $this->_id,
			$request
		);

		return $this->_response->Status() == QuarkDTO::STATUS_200_OK;
	}

	/**
	 * @return QuarkDTO
	 */
	public function Response () {
		return $this->_response;
	}
}