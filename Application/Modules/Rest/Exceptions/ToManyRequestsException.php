<?php
namespace Application\Modules\Rest\Exceptions;

/**
 * Class ToManyRequestsException. Represents an HTTP 429 error.
 * The 429 status code indicates that the user has sent too many
 * requests in a given amount of time ("rate limiting").
 * The response representations SHOULD include details explaining the
 * condition, and MAY include a Retry-After header indicating how long
 * to wait before making a new request...
 *
 * @package Application\Modules\Rest
 * @subpackage    Exceptions
 * @since PHP >=5.6
 * @version 1.0
 * @author Stanislav WEB | Lugansk <stanisov@gmail.com>
 * @filesource /Application/Modules/Rest/Exceptions/ToManyRequestsException.php
 */
class ToManyRequestsException extends BaseException {

    /**
     * @const HTTP response message
     */
    const MESSAGE = 'Too Many Requests';

    /**
     * @const HTTP response code
     */
    const CODE = 429;

    /**
     * Constructor
     *
     * @param array $data additional info
     * @param string $message If no message is given 'Too Many Requests' will be the message
     * @param int $code Status code, defaults to 429
     */
    public function __construct(array $data = [], $message = null, $code = null) {

        if(is_null($message) === true && is_null($code) === true) {

            $message = self::MESSAGE;
            $code = self::CODE;
        }

        parent::__construct($message, $code, $data);
    }
}