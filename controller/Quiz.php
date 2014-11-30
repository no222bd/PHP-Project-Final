<?php

namespace controller;

require_once('model/Question.php');
require_once('view/MessageHandler.php');
require_once('view/Quiz.php');
require_once('model/QuizDAL.php');

class Quiz {

    private $view;
    private $user;
    private $messageHandler;
	private $message;

    public function __construct(\model\User $user) {
        $this->user = $user;
        
        $this->messageHandler = new \view\MessageHandler();
		$this->message = new \view\Message();
		$this->view = new \view\Quiz($this->message);
    }

    public function showQuizStatistics() {

        $quizId = $this->view->getQuizId();

        $quizDAL = new \model\QuizDAL();

        $quiz = $quizDAL->getQuizById($quizId);

        $quizResultArray = $quizDAL->getQuizResultArray($quizId);

        return $this->view->getQuizStatisticsHTML($quiz, $quizResultArray);
    }

    public function showUserStatistics() {

        $userId = $this->view->getUserId();
        $userDAL = new \model\UserDAL();
        $user = $userDAL->getUserById($userId);

        $quizDAL = new \model\QuizDAL();
        $userResultArray = $quizDAL->getUserResultArray($userId);
        
        return $this->view->getUserStatisticsHTML($user, $userResultArray);
    }

    public function manageQuiz() {

        $quizDAL = new \model\QuizDAL();
		
		// Kontrollera om quiz har aktiverats/deaktiverats
        if ($this->view->getAction() == \view\QuizzyMaster::$ACTIVATE_QUIZ || $this->view->getAction() == \view\QuizzyMaster::$DEACTIVATE_QUIZ) {
            $quizDAL->toogleQuizActivation($this->view->getQuizId());///
            $this->messageHandler->setMessage('Quiz-statusen är ändrad');
            
            $this->view->redirect(\view\QuizzyMaster::$MANAGE_QUIZ);
        }

		// Lista skapade quiz
        $quizes = $quizDAL->getAdminQuizes($this->user->getUserId());
        return $this->view->getAdminQuizListHTML($quizes);
    }

    public function showQuizResult() {

        $quizId = $this->view->getQuizId();

        $quizDAL = new \model\QuizDAL();

        $quiz = $quizDAL->getQuizById($quizId);
        $userAnswers = $quizDAL->getUserAnswersArray($this->user->getUserId(), $quizId);
        
        $result = $quizDAL->getQuizResult($quizId, $this->user->getUserId()); 

        return $this->view->getQuizResultHTML($quiz, $userAnswers, $result);
    }

    public function listAvalibleQuiz() {

        $dal = new \model\QuizDAL();
        $quizes = $dal->getAvalibleQuizes($this->user->getUserId());

        return $this->view->getAvalibleQuizListHTML($quizes);
    }

    public function listDoneQuiz() {

        $dal = new \model\QuizDAL();
        $quizes = $dal->getDoneQuizes($this->user->getUserId());

        return $this->view->getDoneQuizListHTML($quizes, $this->user->getUserId(), $dal);
    }

    public function createQuiz() {
      
        $quizId = $this->view->getQuizId();
        
        // Kontrollera om quiz redan är skapat
        if (empty($quizId)) {
		
            // Kontrollera om användaren matat in en titel
            if ($this->view->getQuizTitle() == false) {
                return $this->view->getTitleFormHTML();
            } else {
                // Skapa ett Quiz-objekt
                $title = $this->view->getQuizTitle();
                $quiz = new \model\Quiz($title, $this->user->getUserId());

                $quizDAL = new \model\QuizDAL();
                $quizId = $quizDAL->saveQuiz($quiz);

                $this->view->redirectToQuiz(\view\QuizzyMaster::$CREATE_QUIZ, $quizId);
            }
        } elseif ($this->view->getQuestion()) {
                        
            // Hämta inmatningen från användaren
            $question = $this->view->getQuestion();
            $answers = $this->view->getAnswers();
            
            // Kontrollera om en bild har bifogats
            $mediaPath = false;
            
            if(!$this->view->hasFile()) {
                $hasMedia = false; 
            } else {
                $hasMedia = true;
                $mediaPath = $this->getImagePath();
			}
            
			// Skapa Question-objekt och spara detta i DB
            if($hasMedia == false || $mediaPath != false) {
                
                // Hämta Quiz från DB
                $quizDAL = new \model\QuizDAL();
                $quiz = $quizDAL->getQuizById($quizId);

                // Skapa och lägg till Question i DB
                $questionObject = new \model\Question($question, $answers);

                if($hasMedia)
                    $questionObject->setMediaPath($mediaPath);

                $questionDAL = new \model\QuestionDAL();
                $questionDAL->saveQuestionByQuizId($questionObject, $quizId);

                $this->view->redirectToQuiz(\view\QuizzyMaster::$CREATE_QUIZ, $quizId);
            }
        }
		
	    $questionNumber = count((new \model\QuizDAL())->getQuizById($quizId)->getQuestions()) + 1;
    
		// Visa formulär för skapande av fråga
		return $this->view->getQuestionFormHTML($questionNumber);
    }

    public function doQuiz() {

        $quizId = $this->view->getQuizId();

        // Hämta Quiz och Questions från DB 
        $quizDAL = new \model\QuizDAL();
        $quiz = $quizDAL->getQuizById($quizId);
        $questions = $quiz->getQuestions();

        // Kontrollera att det är POST
        if ($this->view->isPostBack()) {

            $doneQuizId = $quizDAL->getDoneQuizId($this->user->getUserId(), $quizId);

            if (empty($doneQuizId))
                $doneQuizId = $quizDAL->saveDoneQuiz($this->user->getUserId(), $quizId);

            // Hämta svar och questionId från vyn
            $answer = $this->view->getAnswer();
            $questionId = $this->view->getQuestionId();

            // Ta fram AnswerId baserat på svaret
            $questionDAL = new \model\QuestionDAL();
            $answerId = $questionDAL->getAnswerIdByQuestionIdAndAnswer($questionId, $answer);

            // Spara svaret		
            $questionDAL->saveUserAnswer($doneQuizId, $answerId);
        }

        $quizSize = count($questions);
        $answerd = $quizDAL->getUserAnswersArray($this->user->getUserId(), $quizId);

        // Visa nästa obesvarande fråga
        for ($i = 0; $i < $quizSize; $i++) {

            if (array_key_exists($questions[$i]->getQuestionId(), $answerd))
                continue;
            else {
                return $this->view->getHTML($questions[$i], $quizSize, $quiz->getQuizName(), $i + 1);
            }
        }

		// Sätt Quiz-status till Avklarad
        $quizDAL->updateDoneQuizIsComplete($doneQuizId);
        
        $this->view->redirectToQuiz(\view\QuizzyMaster::$SHOW_RESULT, $quiz->getQuizId());
    }
    
    private function getImagePath(){
            
        $image_path = 'media/images/';
        
        $newFile = $this->view->getImage();

        if ($newFile["error"] !== UPLOAD_ERR_OK) {
            $this->message->setMessage('Ett fel inträffade vid uppladdning av bild');
            return false;
        }

        // Ersätt olämpliga tecken med '_'
        $name = preg_replace("/[^A-Z0-9._-]/i", "_", $newFile["name"]);

        // Kontrollera om filnamn redan existrerar
        $i = 0;
        $parts = pathinfo($name);
        while (file_exists($image_path . $name)) {
            $i++;
            $name = $parts["filename"] . "-" . $i . "." . $parts["extension"];
        }
		
		// Kontrollera om filtypen är giltig
		$fileType = exif_imagetype($newFile["tmp_name"]);
		
		if (!in_array($fileType, array(IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG))) {
			$this->message->setMessage('Felaktigt filformat! GIF, JPG & PNG är tillåtna.');
			return false;
		}

        // Lagra bilden på webbservern
        $success = move_uploaded_file($newFile["tmp_name"], $image_path . $name);
        
        if (!$success) {
            $this->message->setMessage('Ett fel inträffade vid sparande av bildfilen');
            return false;
        } else {
            return $image_path . $name;
        }
    }
}
