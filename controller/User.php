<?php

namespace controller;

require_once('view/MessageHandler.php');
require_once('view/User.php');

class User {

    private $view;
    private $messageHandler;

    public function __construct() {
        $this->messageHandler = new \view\MessageHandler();
        $this->view = new \view\User();
    }

    public function manageUser() {

        $userDAL = new \model\UserDAL();

		// Ta bort användare
        if ($_GET[\view\QuizzyMaster::$ACTION] == \view\QuizzyMaster::$DELETE_USER) {
            $userDAL->deleteUserById($_GET[\view\QuizzyMaster::$USER_ID]);
            $this->messageHandler->setMessage('Användare borttagen');
            header('location: ' . \Settings::$ROOT . '?' . \view\QuizzyMaster::$ACTION . '=' . \view\QuizzyMaster::$MANAGE_USER);
        }

		// Uppgradera användare till Administratör
        if ($_GET[\view\QuizzyMaster::$ACTION] == \view\QuizzyMaster::$MAKEADMIN) {
            $userDAL->makeAdminById($_GET[\view\QuizzyMaster::$USER_ID]);
            $this->messageHandler->setMessage('Användare fått administratörsrättigheter');
            header('location: ' . \Settings::$ROOT . '?' . \view\QuizzyMaster::$ACTION . '=' . \view\QuizzyMaster::$MANAGE_USER);
        }

        $users = $userDAL->getUsersOnly();

        return $this->view->getUserListHTML($users);
    }
}
