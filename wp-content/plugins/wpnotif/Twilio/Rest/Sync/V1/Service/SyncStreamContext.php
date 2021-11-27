<?php

/**
 * This code was generated by
 * \ / _    _  _|   _  _
 * | (_)\/(_)(_|\/| |(/_  v1.0.0
 * /       /
 */

namespace Twilio\Rest\Sync\V1\Service;

use Twilio\Exceptions\TwilioException;
use Twilio\InstanceContext;
use Twilio\Options;
use Twilio\Rest\Sync\V1\Service\SyncStream\StreamMessageList;
use Twilio\Values;
use Twilio\Version;

/**
 * PLEASE NOTE that this class contains beta products that are subject to change. Use them with caution.
 *
 * @property \Twilio\Rest\Sync\V1\Service\SyncStream\StreamMessageList streamMessages
 */
class SyncStreamContext extends InstanceContext {
	protected $_streamMessages = null;

	/**
	 * Initialize the SyncStreamContext
	 *
	 * @param \Twilio\Version $version Version that contains the resource
	 * @param string $serviceSid Service Instance SID or unique name.
	 * @param string $sid Stream SID or unique name.
	 *
	 * @return \Twilio\Rest\Sync\V1\Service\SyncStreamContext
	 */
	public function __construct( Version $version, $serviceSid, $sid ) {
		parent::__construct( $version );

		// Path Solution
		$this->solution = array( 'serviceSid' => $serviceSid, 'sid' => $sid, );

		$this->uri = '/Services/' . rawurlencode( $serviceSid ) . '/Streams/' . rawurlencode( $sid ) . '';
	}

	/**
	 * Fetch a SyncStreamInstance
	 *
	 * @return SyncStreamInstance Fetched SyncStreamInstance
	 * @throws TwilioException When an HTTP error occurs.
	 */
	public function fetch() {
		$params = Values::of( array() );

		$payload = $this->version->fetch(
			'GET',
			$this->uri,
			$params
		);

		return new SyncStreamInstance(
			$this->version,
			$payload,
			$this->solution['serviceSid'],
			$this->solution['sid']
		);
	}

	/**
	 * Deletes the SyncStreamInstance
	 *
	 * @return boolean True if delete succeeds, false otherwise
	 * @throws TwilioException When an HTTP error occurs.
	 */
	public function delete() {
		return $this->version->delete( 'delete', $this->uri );
	}

	/**
	 * Update the SyncStreamInstance
	 *
	 * @param array|Options $options Optional Arguments
	 *
	 * @return SyncStreamInstance Updated SyncStreamInstance
	 * @throws TwilioException When an HTTP error occurs.
	 */
	public function update( $options = array() ) {
		$options = new Values( $options );

		$data = Values::of( array( 'Ttl' => $options['ttl'], ) );

		$payload = $this->version->update(
			'POST',
			$this->uri,
			array(),
			$data
		);

		return new SyncStreamInstance(
			$this->version,
			$payload,
			$this->solution['serviceSid'],
			$this->solution['sid']
		);
	}

	/**
	 * Magic getter to lazy load subresources
	 *
	 * @param string $name Subresource to return
	 *
	 * @return \Twilio\ListResource The requested subresource
	 * @throws \Twilio\Exceptions\TwilioException For unknown subresources
	 */
	public function __get( $name ) {
		if ( property_exists( $this, '_' . $name ) ) {
			$method = 'get' . ucfirst( $name );

			return $this->$method();
		}

		throw new TwilioException( 'Unknown subresource ' . $name );
	}

	/**
	 * Magic caller to get resource contexts
	 *
	 * @param string $name Resource to return
	 * @param array $arguments Context parameters
	 *
	 * @return \Twilio\InstanceContext The requested resource context
	 * @throws \Twilio\Exceptions\TwilioException For unknown resource
	 */
	public function __call( $name, $arguments ) {
		$property = $this->$name;
		if ( method_exists( $property, 'getContext' ) ) {
			return call_user_func_array( array( $property, 'getContext' ), $arguments );
		}

		throw new TwilioException( 'Resource does not have a context' );
	}

	/**
	 * Provide a friendly representation
	 *
	 * @return string Machine friendly representation
	 */
	public function __toString() {
		$context = array();
		foreach ( $this->solution as $key => $value ) {
			$context[] = "$key=$value";
		}

		return '[Twilio.Sync.V1.SyncStreamContext ' . implode( ' ', $context ) . ']';
	}

	/**
	 * Access the streamMessages
	 *
	 * @return \Twilio\Rest\Sync\V1\Service\SyncStream\StreamMessageList
	 */
	protected function getStreamMessages() {
		if ( ! $this->_streamMessages ) {
			$this->_streamMessages = new StreamMessageList(
				$this->version,
				$this->solution['serviceSid'],
				$this->solution['sid']
			);
		}

		return $this->_streamMessages;
	}
}