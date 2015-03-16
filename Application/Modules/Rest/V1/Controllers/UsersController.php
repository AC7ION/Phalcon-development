<?php
namespace Application\Modules\Rest\V1\Controllers;

/**
 * Class UsersController
 *
 * @package    Application\Modules\Rest
 * @subpackage    Controllers
 * @since PHP >=5.4
 * @version 1.0
 * @author Stanislav WEB | Lugansk <stanisov@gmail.com>
 * @filesource /Application/Modules/Rest/Controllers/UsersController.php
 */
class UsersController extends ControllerBase {

    /**
     * User profile action
     */
    public function indexAction() {

        $this->rest->setMessage(
            $this->getDI()->get('UserMapper')->read()->toArray()
        );
    }

    /**
     * User profile action
     */
    public function accessAction() {
        $this->rest->setMessage(
            $this->getDI()->get('AuthService')->getAccessToken()
        );
    }

}