<?php

namespace controller;

require_once('view/QuizzyMaster.php');
require_once('controller/Quiz.php');
require_once('controller/User.php');
require_once('login/controller/LoginController.php');
require_once('login/controller/Register.php'); 

class QuizzyMaster {

	private $view;
	private $login_controller;

	public function __construct() {
		$this->login_controller = new \login\controller\LoginController();
	}

	public function doRoute() {

		// KOntrollera om användaren är inloggad
		if($this->login_controller->checkLoginStatus()) {

			// Hämta användaren och skicka vidare till vyn
			$user = $this->login_controller->getUser();
			$this->view = new \view\QuizzyMaster($user);

			// Kolla om användaren är User eller Adminstrator
			if($user->getIsAdmin()) {

				// Administratörs alternativ

				switch (\view\QuizzyMaster::getAction()) {

					case \view\QuizzyMaster::$MANAGE_USER:
						return (new \controller\User())->manageUser();
						break;
					case \view\QuizzyMaster::$DELETE_USER:
						return (new \controller\User())->manageUser();
						break;		
					case \view\QuizzyMaster::$MAKEADMIN:
						return (new \controller\User())->manageUser();
						break;
					case \view\QuizzyMaster::$MANAGE_QUIZ:
						return (new \controller\Quiz($user))->manageQuiz();
						break;
					case \view\QuizzyMaster::$DELETE_QUIZ:
						return (new \controller\Quiz($user))->manageQuiz();
						break;	
					case \view\QuizzyMaster::$CREATE_QUIZ:
						return (new \controller\Quiz($user))->createQuiz();
						break;
					case \view\QuizzyMaster::$ACTIVATE_QUIZ:
						return (new \controller\Quiz($user))->manageQuiz();
						break;	
					case \view\QuizzyMaster::$DEACTIVATE_QUIZ:
						return (new \controller\Quiz($user))->manageQuiz();
						break;
					case \view\QuizzyMaster::$QUIZ_STATS:
						return (new \controller\Quiz($user))->showQuizStatistics();
						break;	
					case \view\QuizzyMaster::$USER_STATS:
						return (new \controller\Quiz($user))->showUserStatistics();
						break;
					default:
						return  $this->view->getAdminHTML();
						break;
				}

			} else {

				// Användare alternativ

				switch (\view\QuizzyMaster::getAction()) {
					
					case \view\QuizzyMaster::$LIST_AVALIBLE:
						return (new \controller\Quiz($user))->listAvalibleQuiz();
						break;
					case \view\QuizzyMaster::$LIST_DONE:
						return (new \controller\Quiz($user))->listDoneQuiz();
						break;
					case \view\QuizzyMaster::$SHOW_RESULT:
						return (new \controller\Quiz($user))->showQuizResult();
						break;
					case \view\QuizzyMaster::$DO_QUIZ:
						return (new \controller\Quiz($user))->doQuiz();
						break;
					default:
						return  $this->view->getUserHTML();
						break;
				}
			}
		}

		// Kontrollera om användaren vill göra en registrering
		if(\view\QuizzyMaster::getAction() == \view\QuizzyMaster::$REGISTER) {
			return (new \login\controller\Register())->doRegister();
		}
		else {
			return $this->login_controller->getLoginHTML();
		}
	}
}