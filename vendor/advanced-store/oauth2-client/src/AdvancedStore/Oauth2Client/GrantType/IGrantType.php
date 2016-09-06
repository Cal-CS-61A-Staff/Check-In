<?php
/**
 * @package OAuth2 Client
 * @author  advanced STORE GmbH
 * Date:    03.04.14
 */

namespace AdvancedStore\Oauth2Client\GrantType;

/**
 * Specific GrantType Interface
 */
interface IGrantType {

	/**
	 * Adds a specific Handling of the parameters
	 *
	 * @return array of Specific parameters to be sent.
	 * @param  mixed  $parameters the parameters array (passed by reference)
	 */
	public function validateParameters(&$parameters);
}