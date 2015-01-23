<?php
namespace Modules\Frontend\Controllers;
use Phalcon\Mvc\View;
use Models\Users;

/**
 * Class SignController
 * @package    Frontend
 * @subpackage    Modules\Frontend\Controllers
 * @since PHP >=5.4
 * @version 1.0
 * @author Stanislav WEB | Lugansk <stanisov@gmail.com>
 * @filesource /apps/Modules/Frontend/Controllers/SignController.php
 */
class SignController extends ControllerBase
{

    /**
     * initialize() Initialize constructor
     * @access public
     * @return null
     */
    public function initialize()
    {
        parent::initialize();
    }

    /**
     * indexAction() Check auth action
     * @access public
     * @return null
     */
    public function indexAction()
    {

        if($this->request->isAjax() === true) {

            if($this->request->isPost() === true) {

                if ($this->security->checkToken()) {

                    // The token is ok, check sign type
                    $type   =   $this->request->getPost('type');

                    if($type === 'signin') {

                        // login
                        $this->login();
                    }
                    else {
                        // register
                        //$this->register();
                    }
                }
                else
                {
                    // If CSRF request was broken

                    if ($this->config->logger->enable)
                        $this->logger->error('Authenticate failed from ' . $this->request->getClientAddress() . '. CSRF attack');

                    $this->setReply(['message' => 'Invalid access token! Reload please']);
                }
            }
            else
            {
                // if the request is different from POST

                if ($this->config->logger->enable)
                    $this->logger->error('Authenticate failed from ' . $this->request->getClientAddress() . '. Wrong auth method');

                $this->setReply(['message' => 'Could not resolve request']);
            }

            return $this->getReply();
        }
        else {

            // render the view
            $this->view->pick('error/uncaughtException');

        }
    }

    /**
     * Logout action to destroy user auth data
     *
     * @access public
     * @return null
     */
    public function logoutAction() {

        // destroy auth data
        $this->clearUserData();

        if($this->request->isAjax() === false) {
            $this->response->redirect('/');
        }
        else {

            $this->setReply(['success' => true]);
        }

        return $this->getReply();
    }

    /**
     * Authorization to customer area
     */
    protected function login() {

        $login = $this->request->getPost('login', 'trim');
        $password = $this->request->getPost('password', 'trim');

        $user = (new Users())->findFirst([
            "login = ?0",
            "bind" => [$login],
        ]);

        if(empty($user) === false) {

            if($this->security->checkHash($password, $user->getPassword()))
            {
                // user founded, password checked. Set auth token

                $token = md5($user->getLogin() . $user->getPassword() . $this->request->getUserAgent());

                // setup user cookies and send to client for update
                $this->cookies->set('token', $token, time() + ($this->config->rememberKeep), '/', $this->engine->getHost(), false, false);
                $this->session->set('token', $token);
                $this->session->set('user', $user);

                // update auth params
                $user->setDateLastvisit(date('Y-m-d H:i:s'))
                    ->setToken($token)
                    ->setIp($this->request->getClientAddress())
                    ->setUa($this->request->getUserAgent())
                    ->save();

                if ($this->config->logger->enable) {
                    $this->logger->log('Authenticate success from ' . $this->request->getClientAddress());
                }

                // send reply to client
                $this->setReply([
                    'user'  => [
                        'id'        =>  $user->getId(),
                        'login'     =>  $user->getLogin(),
                        'name'      =>  $user->getName(),
                        'surname'   =>  $user->getSurname(),
                        'state'     =>  $user->getState(),
                        'rating'    =>  $user->getRating(),
                        'surname'   =>  $user->getSurname(),
                        'date_registration' =>  $user->getDateRegistration(),
                        'date_lastvisit'    =>  $user->getDateLastvisit()
                    ],
                    'token'     => $token,
                    'success'   => true
                ]);
            }
            else
            {
                // wrong authenticate data (password or login)
                if($this->config->logger->enable) {
                    $this->logger->error('Authenticate failed from ' . $this->request->getClientAddress() . '. Wrong authenticate data');
                }

                $this->setReply(['message'   => 'Wrong authenticate data']);
            }
        }
        else
        {

            if($this->config->logger->enable)
                $this->logger->error('Authenticate failed from ' . $this->request->getClientAddress() . '. The user ' . $login . ' not found');

            // user does not exist in database
            $this->setReply(['message' => 'The user not found']);
        }
    }


    /**
     * Clear all auth user data
     *
     * @access protected
     * @return null
     */
    protected function clearUserData() {

        $this->user = null;

        // destroy session data
        if($this->session->has('user')) {

            $this->session->remove('user');
        }

        // destroy cookies
        if($this->cookies->has('token')) {

            $this->cookies->get('token')->delete();

        }
    }

    public function validateAction() {

    }

    public function restoreAction() {

    }
}

