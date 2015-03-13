<?php
namespace Application\Modules\Rest\Controllers;

use \Phalcon\Mvc\Controller;
use \Phalcon\Http\Response\Exception as RestException;

/**
 * Class ControllerBase
 *
 * @package    Application\Modules\Rest
 * @subpackage    Controllers
 * @since PHP >=5.4
 * @version 1.0
 * @author Stanislav WEB | Lugansk <stanisov@gmail.com>
 * @filesource /Application/Modules/Rest/Controllers/ControllerBase.php
 */
class ControllerBase extends Controller
{
    /**
     * Rest service
     *
     * @var \Application\Services\Http\JsonRestService $rest
     */
    protected $rest;

    /**
     * Required params to access for actions
     * Can overload for each controller's method
     *
     * @var array $required
     */
    protected $required = [];

    /**
     * Event before routes execute
     */
    public function beforeExecuteRoute()
    {

        // define Rest service
        $this->rest = $this->getDI()->get("JsonRestService");

        var_dump($this->rest);
        try {

            $this->rest->setDebug(false);
            $this->rest->filterRequiredParams($this->required);
            $this->rest->useRestrictAccess();
        }
        catch(RestException $e) {

            var_dump($e);
            $this->rest->setStatusMessage($e->getCode(), $e->getMessage());
        }
    }

    /**
     * Send response collection put from controllers
     *
     * @return \Phalcon\Http\ResponseInterface
     */
    public function afterExecuteRoute()
    {
        $this->rest->response();
    }
}
