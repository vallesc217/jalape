<?php

/**
 * This code was generated by
 * \ / _    _  _|   _  _
 * | (_)\/(_)(_|\/| |(/_  v1.0.0
 * /       /
 */

namespace Twilio\Rest\Trunking\V1;

use Twilio\Options;
use Twilio\Values;

abstract class TrunkOptions {
	/**
	 * @param string $friendlyName A human-readable name for the Trunk.
	 * @param string $domainName The unique address you reserve on Twilio to which
	 *                           you route your SIP traffic.
	 * @param string $disasterRecoveryUrl The HTTP URL that Twilio will request if
	 *                                    an error occurs while sending SIP traffic
	 *                                    towards your configured Origination URL.
	 * @param string $disasterRecoveryMethod The HTTP method Twilio will use when
	 *                                       requesting the DisasterRecoveryUrl.
	 * @param string $recording The recording settings for this trunk.
	 * @param boolean $secure The Secure Trunking  settings for this trunk.
	 * @param boolean $cnamLookupEnabled The Caller ID Name (CNAM) lookup setting
	 *                                   for this trunk.
	 *
	 * @return CreateTrunkOptions Options builder
	 */
	public static function create( $friendlyName = Values::NONE, $domainName = Values::NONE, $disasterRecoveryUrl = Values::NONE, $disasterRecoveryMethod = Values::NONE, $recording = Values::NONE, $secure = Values::NONE, $cnamLookupEnabled = Values::NONE ) {
		return new CreateTrunkOptions( $friendlyName, $domainName, $disasterRecoveryUrl, $disasterRecoveryMethod, $recording, $secure, $cnamLookupEnabled );
	}

	/**
	 * @param string $friendlyName A human-readable name for the Trunk.
	 * @param string $domainName The unique address you reserve on Twilio to which
	 *                           you route your SIP traffic.
	 * @param string $disasterRecoveryUrl The HTTP URL that Twilio will request if
	 *                                    an error occurs while sending SIP traffic
	 *                                    towards your configured Origination URL.
	 * @param string $disasterRecoveryMethod The HTTP method Twilio will use when
	 *                                       requesting the DisasterRecoveryUrl.
	 * @param string $recording The recording settings for this trunk.
	 * @param boolean $secure The Secure Trunking  settings for this trunk.
	 * @param boolean $cnamLookupEnabled The Caller ID Name (CNAM) lookup setting
	 *                                   for this trunk.
	 *
	 * @return UpdateTrunkOptions Options builder
	 */
	public static function update( $friendlyName = Values::NONE, $domainName = Values::NONE, $disasterRecoveryUrl = Values::NONE, $disasterRecoveryMethod = Values::NONE, $recording = Values::NONE, $secure = Values::NONE, $cnamLookupEnabled = Values::NONE ) {
		return new UpdateTrunkOptions( $friendlyName, $domainName, $disasterRecoveryUrl, $disasterRecoveryMethod, $recording, $secure, $cnamLookupEnabled );
	}
}

class CreateTrunkOptions extends Options {
	/**
	 * @param string $friendlyName A human-readable name for the Trunk.
	 * @param string $domainName The unique address you reserve on Twilio to which
	 *                           you route your SIP traffic.
	 * @param string $disasterRecoveryUrl The HTTP URL that Twilio will request if
	 *                                    an error occurs while sending SIP traffic
	 *                                    towards your configured Origination URL.
	 * @param string $disasterRecoveryMethod The HTTP method Twilio will use when
	 *                                       requesting the DisasterRecoveryUrl.
	 * @param string $recording The recording settings for this trunk.
	 * @param boolean $secure The Secure Trunking  settings for this trunk.
	 * @param boolean $cnamLookupEnabled The Caller ID Name (CNAM) lookup setting
	 *                                   for this trunk.
	 */
	public function __construct( $friendlyName = Values::NONE, $domainName = Values::NONE, $disasterRecoveryUrl = Values::NONE, $disasterRecoveryMethod = Values::NONE, $recording = Values::NONE, $secure = Values::NONE, $cnamLookupEnabled = Values::NONE ) {
		$this->options['friendlyName']           = $friendlyName;
		$this->options['domainName']             = $domainName;
		$this->options['disasterRecoveryUrl']    = $disasterRecoveryUrl;
		$this->options['disasterRecoveryMethod'] = $disasterRecoveryMethod;
		$this->options['recording']              = $recording;
		$this->options['secure']                 = $secure;
		$this->options['cnamLookupEnabled']      = $cnamLookupEnabled;
	}

	/**
	 * A human-readable name for the Trunk.
	 *
	 * @param string $friendlyName A human-readable name for the Trunk.
	 *
	 * @return $this Fluent Builder
	 */
	public function setFriendlyName( $friendlyName ) {
		$this->options['friendlyName'] = $friendlyName;

		return $this;
	}

	/**
	 * The unique address you reserve on Twilio to which you route your SIP traffic. Domain names can contain letters, digits, and `-` and must always end with `pstn.twilio.com`. See [Termination Settings](https://www.twilio.com/docs/sip-trunking/getting-started#termination) for more information.
	 *
	 * @param string $domainName The unique address you reserve on Twilio to which
	 *                           you route your SIP traffic.
	 *
	 * @return $this Fluent Builder
	 */
	public function setDomainName( $domainName ) {
		$this->options['domainName'] = $domainName;

		return $this;
	}

	/**
	 * The HTTP URL that Twilio will request if an error occurs while sending SIP traffic towards your configured Origination URL. Twilio will retrieve TwiML from this URL and execute those instructions like any other normal TwiML call. See [Disaster Recovery](https://www.twilio.com/docs/sip-trunking/getting-started#disaster-recovery) for more information.
	 *
	 * @param string $disasterRecoveryUrl The HTTP URL that Twilio will request if
	 *                                    an error occurs while sending SIP traffic
	 *                                    towards your configured Origination URL.
	 *
	 * @return $this Fluent Builder
	 */
	public function setDisasterRecoveryUrl( $disasterRecoveryUrl ) {
		$this->options['disasterRecoveryUrl'] = $disasterRecoveryUrl;

		return $this;
	}

	/**
	 * The HTTP method Twilio will use when requesting the `DisasterRecoveryUrl`. Either `GET` or `POST`.
	 *
	 * @param string $disasterRecoveryMethod The HTTP method Twilio will use when
	 *                                       requesting the DisasterRecoveryUrl.
	 *
	 * @return $this Fluent Builder
	 */
	public function setDisasterRecoveryMethod( $disasterRecoveryMethod ) {
		$this->options['disasterRecoveryMethod'] = $disasterRecoveryMethod;

		return $this;
	}

	/**
	 * The recording settings for this trunk. If turned on, all calls going through this trunk will be recorded and the recording can either start when the call is ringing or when the call is answered. See [Recording](https://www.twilio.com/docs/sip-trunking/getting-started#recording) for more information.
	 *
	 * @param string $recording The recording settings for this trunk.
	 *
	 * @return $this Fluent Builder
	 */
	public function setRecording( $recording ) {
		$this->options['recording'] = $recording;

		return $this;
	}

	/**
	 * The Secure Trunking  settings for this trunk. If turned on, all calls going through this trunk will be secure using SRTP for media and TLS for signalling. If turned off, then RTP will be used for media. See [Secure Trunking](https://www.twilio.com/docs/sip-trunking/getting-started#securetrunking) for more information.
	 *
	 * @param boolean $secure The Secure Trunking  settings for this trunk.
	 *
	 * @return $this Fluent Builder
	 */
	public function setSecure( $secure ) {
		$this->options['secure'] = $secure;

		return $this;
	}

	/**
	 * The Caller ID Name (CNAM) lookup setting for this trunk. If turned on, all inbound calls to this SIP Trunk from the United States and Canada will automatically perform a CNAM Lookup and display Caller ID data on your phone. See [CNAM](https://www.twilio.com/docs/sip-trunking#CNAM) Lookups for more information.
	 *
	 * @param boolean $cnamLookupEnabled The Caller ID Name (CNAM) lookup setting
	 *                                   for this trunk.
	 *
	 * @return $this Fluent Builder
	 */
	public function setCnamLookupEnabled( $cnamLookupEnabled ) {
		$this->options['cnamLookupEnabled'] = $cnamLookupEnabled;

		return $this;
	}

	/**
	 * Provide a friendly representation
	 *
	 * @return string Machine friendly representation
	 */
	public function __toString() {
		$options = array();
		foreach ( $this->options as $key => $value ) {
			if ( $value != Values::NONE ) {
				$options[] = "$key=$value";
			}
		}

		return '[Twilio.Trunking.V1.CreateTrunkOptions ' . implode( ' ', $options ) . ']';
	}
}

class UpdateTrunkOptions extends Options {
	/**
	 * @param string $friendlyName A human-readable name for the Trunk.
	 * @param string $domainName The unique address you reserve on Twilio to which
	 *                           you route your SIP traffic.
	 * @param string $disasterRecoveryUrl The HTTP URL that Twilio will request if
	 *                                    an error occurs while sending SIP traffic
	 *                                    towards your configured Origination URL.
	 * @param string $disasterRecoveryMethod The HTTP method Twilio will use when
	 *                                       requesting the DisasterRecoveryUrl.
	 * @param string $recording The recording settings for this trunk.
	 * @param boolean $secure The Secure Trunking  settings for this trunk.
	 * @param boolean $cnamLookupEnabled The Caller ID Name (CNAM) lookup setting
	 *                                   for this trunk.
	 */
	public function __construct( $friendlyName = Values::NONE, $domainName = Values::NONE, $disasterRecoveryUrl = Values::NONE, $disasterRecoveryMethod = Values::NONE, $recording = Values::NONE, $secure = Values::NONE, $cnamLookupEnabled = Values::NONE ) {
		$this->options['friendlyName']           = $friendlyName;
		$this->options['domainName']             = $domainName;
		$this->options['disasterRecoveryUrl']    = $disasterRecoveryUrl;
		$this->options['disasterRecoveryMethod'] = $disasterRecoveryMethod;
		$this->options['recording']              = $recording;
		$this->options['secure']                 = $secure;
		$this->options['cnamLookupEnabled']      = $cnamLookupEnabled;
	}

	/**
	 * A human-readable name for the Trunk.
	 *
	 * @param string $friendlyName A human-readable name for the Trunk.
	 *
	 * @return $this Fluent Builder
	 */
	public function setFriendlyName( $friendlyName ) {
		$this->options['friendlyName'] = $friendlyName;

		return $this;
	}

	/**
	 * The unique address you reserve on Twilio to which you route your SIP traffic. Domain names can contain letters, digits, and `-` and must always end with `pstn.twilio.com`. See [Termination Settings](https://www.twilio.com/docs/sip-trunking/getting-started#termination) for more information.
	 *
	 * @param string $domainName The unique address you reserve on Twilio to which
	 *                           you route your SIP traffic.
	 *
	 * @return $this Fluent Builder
	 */
	public function setDomainName( $domainName ) {
		$this->options['domainName'] = $domainName;

		return $this;
	}

	/**
	 * The HTTP URL that Twilio will request if an error occurs while sending SIP traffic towards your configured Origination URL. Twilio will retrieve TwiML from this URL and execute those instructions like any other normal TwiML call. See [Disaster Recovery](https://www.twilio.com/docs/sip-trunking/getting-started#disaster-recovery) for more information.
	 *
	 * @param string $disasterRecoveryUrl The HTTP URL that Twilio will request if
	 *                                    an error occurs while sending SIP traffic
	 *                                    towards your configured Origination URL.
	 *
	 * @return $this Fluent Builder
	 */
	public function setDisasterRecoveryUrl( $disasterRecoveryUrl ) {
		$this->options['disasterRecoveryUrl'] = $disasterRecoveryUrl;

		return $this;
	}

	/**
	 * The HTTP method Twilio will use when requesting the `DisasterRecoveryUrl`. Either `GET` or `POST`.
	 *
	 * @param string $disasterRecoveryMethod The HTTP method Twilio will use when
	 *                                       requesting the DisasterRecoveryUrl.
	 *
	 * @return $this Fluent Builder
	 */
	public function setDisasterRecoveryMethod( $disasterRecoveryMethod ) {
		$this->options['disasterRecoveryMethod'] = $disasterRecoveryMethod;

		return $this;
	}

	/**
	 * The recording settings for this trunk. If turned on, all calls going through this trunk will be recorded and the recording can either start when the call is ringing or when the call is answered. See [Recording](https://www.twilio.com/docs/sip-trunking/getting-started#recording) for more information.
	 *
	 * @param string $recording The recording settings for this trunk.
	 *
	 * @return $this Fluent Builder
	 */
	public function setRecording( $recording ) {
		$this->options['recording'] = $recording;

		return $this;
	}

	/**
	 * The Secure Trunking  settings for this trunk. If turned on, all calls going through this trunk will be secure using SRTP for media and TLS for signalling. If turned off, then RTP will be used for media. See [Secure Trunking](https://www.twilio.com/docs/sip-trunking/getting-started#securetrunking) for more information.
	 *
	 * @param boolean $secure The Secure Trunking  settings for this trunk.
	 *
	 * @return $this Fluent Builder
	 */
	public function setSecure( $secure ) {
		$this->options['secure'] = $secure;

		return $this;
	}

	/**
	 * The Caller ID Name (CNAM) lookup setting for this trunk. If turned on, all inbound calls to this SIP Trunk from the United States and Canada will automatically perform a CNAM Lookup and display Caller ID data on your phone. See [CNAM](https://www.twilio.com/docs/sip-trunking#CNAM) Lookups for more information.
	 *
	 * @param boolean $cnamLookupEnabled The Caller ID Name (CNAM) lookup setting
	 *                                   for this trunk.
	 *
	 * @return $this Fluent Builder
	 */
	public function setCnamLookupEnabled( $cnamLookupEnabled ) {
		$this->options['cnamLookupEnabled'] = $cnamLookupEnabled;

		return $this;
	}

	/**
	 * Provide a friendly representation
	 *
	 * @return string Machine friendly representation
	 */
	public function __toString() {
		$options = array();
		foreach ( $this->options as $key => $value ) {
			if ( $value != Values::NONE ) {
				$options[] = "$key=$value";
			}
		}

		return '[Twilio.Trunking.V1.UpdateTrunkOptions ' . implode( ' ', $options ) . ']';
	}
}