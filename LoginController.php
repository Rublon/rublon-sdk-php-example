<?php

use Rublon\Core\Exceptions\Api\UserBypassedException;
use Rublon\Core\Exceptions\RublonException;
use Rublon\Rublon;
use Rublon\RublonCallback;

class LoginController
{
    private $cfg;

    const NOTICE = 'alert-primary';
    const SUCCESS = 'alert-success';
    const ERROR = 'alert-danger';
    const WARNING = 'alert-warning';

    /**
     * LoginController constructor.
     * @param array $cfg
     */
    public function __construct($cfg)
    {
        session_name('rublon-sdk-example');
        session_start();

        $this->setConfig($cfg);

        if (isset($_GET['rublon'])) {
            $rublonParam = $_GET['rublon'];
        }

        if (!empty($rublonParam)) {
            if ($rublonParam === 'callback') {
                $this->callback();
            } else if ($rublonParam === 'passwordlessCallback') {
                $this->passwordlessCallback();
            }
        }
    }

    public function setConfig($cfg)
    {
        $this->cfg = (object)$cfg;

        if (self::isValidConfig() === false) {
            ?>
            <div class="alert alert-warning align-center" role="alert">Please first fill config file.</div>
            <?php
            exit;
        }
    }

    /**
     * Method to validate config
     *
     * @return bool
     */
    private function isValidConfig()
    {
        $fields = array(
            'RUBLON_SYSTEM_TOKEN',
            'RUBLON_SECRET_KEY',
            'USER_PASSWORD',
            'RUBLON_API_SERVER'
        );

        foreach ($fields as $field) {
            if (empty($this->cfg->{$field})) {
                return false;
            }
        }

        return true;
    }

    public function callback()
    {
        try {
            $rublon = new Rublon($this->cfg->RUBLON_SYSTEM_TOKEN, $this->cfg->RUBLON_SECRET_KEY, $this->cfg->RUBLON_API_SERVER);
            $callback = new RublonCallback($rublon);

            $callback->call(
                $successHandler = function ($appUserId, RublonCallback $callback) {
                    $_SESSION['user'] = $appUserId;
                },
                $cancelHandler = function (RublonCallback $callback) {
                    $_SESSION['flashMsg'] = array('text' => 'Request cancelled', 'type' => self::WARNING);
                }
            );

        } catch (RublonException $e) {
            $_SESSION['flashMsg'] = array('text' => $e->getMessage(), 'type' => self::ERROR);
        }

        header('Location: index.php');
    }

    public function passwordlessCallback()
    {
        try {
            $rublon = new Rublon($this->cfg->RUBLON_SYSTEM_TOKEN, $this->cfg->RUBLON_SECRET_KEY, $this->cfg->RUBLON_API_SERVER);
            $callback = new RublonCallback($rublon);

            $callback->call(
                $successHandler = function ($appUserId, RublonCallback $callback) {
                    $_SESSION['user'] = $appUserId;
                },
                $cancelHandler = function (RublonCallback $callback) {
                    $_SESSION['flashMsg'] = array('text' => 'Request cancelled', 'type' => self::WARNING);
                }
            );

        } catch (RublonException $e) {
            $_SESSION['flashMsg'] = array('text' => $e->getMessage(), 'type' => self::ERROR);
        }

        header('Location: passwordless.php');
    }

    /**
     * @param $userEmail
     */
    public function authentication($userEmail)
    {
        // Make sure that the user is not logged-in:
        unset($_SESSION['user']);

        /* initialize the Rublon authentication */
        try {
            $rublon = new Rublon($this->cfg->RUBLON_SYSTEM_TOKEN, $this->cfg->RUBLON_SECRET_KEY, $this->cfg->RUBLON_API_SERVER);

            $url = $rublon->auth(
                $callbackUrl = $this->returnSiteUrl() . '?rublon=callback', $userEmail, $userEmail,
                ["logoutUrl" => $this->returnSiteUrl()]
            );

            if (!empty($url)) {
                // Redirect the user's browser to the Rublon's server to authenticate by Rublon:
                header('Location: ' . $url);
                exit;
            } else {
                $_SESSION['flashMsg'] = array('text' => 'Logging error', 'type' => self::ERROR);
                header('Location: index.php');
            }
        } catch (UserBypassedException $e) {
            if (!empty($userEmail)) {
                $_SESSION['flashMsg'] = array('text' => 'User bypassed', 'type' => self::NOTICE);
            }

            header('Location: index.php');
        } catch (RublonException $e) {
            $_SESSION['flashMsg'] = array('text' => $e->getMessage(), 'type' => self::ERROR);
            header('Location: index.php');
        }
    }

    /**
     * @return mixed
     */
    public function passwordlessLogin()
    {
        // Make sure that the user is not logged-in:
        unset($_SESSION['user']);

        $rublon = new Rublon($this->cfg->RUBLON_SYSTEM_TOKEN, $this->cfg->RUBLON_SECRET_KEY, $this->cfg->RUBLON_API_SERVER);
        $userEmail = (!empty($_POST['userEmail'])) ? $_POST['userEmail'] : null;

        try {
            $consumerParams = array("logoutUrl" => $this->returnSiteUrl() . '?logout');

            $url = $rublon->auth(
                $callbackUrl = $this->returnSiteUrl() . '?rublon=passwordlessCallback', $userEmail, $userEmail, $consumerParams, true
            );

            if (!empty($url)) {
                header('Location: ' . $url);
                exit;
            } else {
                $_SESSION['flashMsg'] = array('text' => 'Logging error', 'type' => self::ERROR);
                header('Location: passwordless.php');
            }
        } catch (UserBypassedException $e) {
            if (!empty($userEmail)) {
                $_SESSION['flashMsg'] = array('text' => 'User bypassed', 'type' => self::NOTICE);
                $_SESSION['user'] = $userEmail;
            }

            header('Location: passwordless.php');
        } catch (RublonException $e) {
            $_SESSION['flashMsg'] = array('text' => $e->getMessage(), 'type' => self::ERROR);
            header('Location: passwordless.php');
            exit;
        }

        $data['rublonLoginBox'] = $rublon->getWidget(true);
        return $data;
    }

    /**
     * @param $key
     * @return string
     */
    public function returnConfigValueByKey($key)
    {
        return $this->cfg->{$key};
    }

    /**
     * @return string
     */
    public function renderWidget()
    {
        $rublon = new Rublon($this->cfg->RUBLON_SYSTEM_TOKEN, $this->cfg->RUBLON_SECRET_KEY, $this->cfg->RUBLON_API_SERVER);

        $data['rublonLoginBox'] = $rublon->getWidget(true);
        return $data;
    }

    /**
     * @return string
     */
    public function returnSiteUrl()
    {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] === 443) ? "https://" : "http://";
        return $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
    }
}