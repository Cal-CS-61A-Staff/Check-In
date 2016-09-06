<?php
/**
 * @package OAuth2 Client
 * @author  advanced STORE GmbH
 * Date:    03.04.14
 */

namespace AdvancedStore\Oauth2Client\Exceptions;

class InvalidArgumentException extends \InvalidArgumentException {

	const INVALID_GRANT_TYPE      = 0x01;
	const CERTIFICATE_NOT_FOUND   = 0x02;
	const REQUIRE_PARAMS_AS_ARRAY = 0x03;
	const MISSING_PARAMETER       = 0x04;

} 