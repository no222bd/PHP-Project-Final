<?php

namespace view;

require_once('view/Question.php');
require_once('view/Message.php');

class Quiz {

    private $messageHandler;
	private $message;

	private static $QUIZ_TITLE = 'quizTitle';
	private static $QUESTION = 'question';
	private static $ANSWER_1 = 'answer1';
	private static $ANSWER_2 = 'answer2';
	private static $ANSWER_3 = 'answer3';
	private static $ANSWER_4 = 'answer4';
	private static $NEW_FILE = 'newFile';
	private static $QUIZ_MIN_SIZE = 3;
	
			
    public function __construct(\view\Message $message) {
        $this->messageHandler = new \view\MessageHandler();
		$this->message = $message;
    }

    public function redirectToUser($action, $id) {
        header('location: ' . \Settings::$ROOT . '?' . \view\QuizzyMaster::$ACTION . '=' . $action . '&'
                            . \view\QuizzyMaster::$USER_ID . '=' . $id);
    }

    public function redirectToQuiz($action, $id) {
        header('location: ' . \Settings::$ROOT . '?' . \view\QuizzyMaster::$ACTION . '=' . $action . '&'
                            . \view\QuizzyMaster::$QUIZ_ID . '=' . $id);
    }

    public function redirect($action) {
        header('location: ' . \Settings::$ROOT . '?' . \view\QuizzyMaster::$ACTION . '=' . $action);
    }

    public function getQuizId() {
        if (!empty($_GET[\view\QuizzyMaster::$QUIZ_ID]))
            return $_GET[\view\QuizzyMaster::$QUIZ_ID];
        else
            return NULL;
    }

    public function getUserId() {
        if (!empty($_GET[\view\QuizzyMaster::$USER_ID]))
            return $_GET[\view\QuizzyMaster::$USER_ID];
        else
            return NULL;
    }
	
	public function getAction() {
		if (!empty($_GET[\view\QuizzyMaster::$ACTION]))
            return $_GET[\view\QuizzyMaster::$ACTION];
        else
            return NULL;
	}
	
	public function hasFile() {
		if(empty($_FILES[self::$NEW_FILE]["name"]))
			return false;
		else
			return true;
	}
	
	public function isPostBack() {
        return $_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST[\view\QuizzyMaster::$ANSWER]);
    }

    public function getAnswer() {
        return $_POST[\view\QuizzyMaster::$ANSWER];
    }

    public function getQuestionId() {
        return $_POST[\view\QuizzyMaster::$QUESTION_ID];
    }

	// Visa resultat
    public function getResultHTML($correctCount, $questionIdex) {
        $html = '<p>Du hade ' . $correctCount . ' av ' . $questionIdex . ' rätt!</p>';

        $html .= '<a href="' . \Settings::$ROOT . '">Meny</a';

        return $html;
    }

	// Visa fråga
    public function getHTML(\model\Question $question, $numberOfQuestions, $quizName, $questionNumber) {
        $html = '<h3><a href="' . \Settings::$ROOT . '">Meny</a></h3>
				 <h2>' . $quizName . '</h2>';

        $html .= '<div id="questionBox">'
					. (new \view\Question())->getHTML($question) .
                '</div>';

        $html .= '<h3>Fråga ' . $questionNumber . ' / ' . $numberOfQuestions . '</h3>';

        return $html;
    }

    public function getImage() {
		if(isset($_FILES[self::$NEW_FILE]))
			return $_FILES[self::$NEW_FILE];
	}

    public function getQuizTitle() {
        if (isset($_POST[self::$QUIZ_TITLE]))
            return $_POST[self::$QUIZ_TITLE];
        else
            return NULL;
    }

    public function getQuestion() {
        if (isset($_POST[self::$QUESTION]))
            return $_POST[self::$QUESTION];
        else
            return NULL;
    }

    public function getAnswers() {
        $answers = array();

        $answers[] = $_POST[self::$ANSWER_1];
        $answers[] = $_POST[self::$ANSWER_2];
        $answers[] = $_POST[self::$ANSWER_3];
        $answers[] = $_POST[self::$ANSWER_4];

        return $answers;
    }

	// Visa formulär för Quiz titel
    public function getTitleFormHTML() {
	
        $html = '<h3><a href="' . \Settings::$ROOT . '">Meny</a></h3>
				 <h2>Skapa Quiz</h2>';

        $html .= '<form id="questionForm" method="POST">
					<label for="title">Ange namn på Quiz</label>
					<input id="title" type="text" name="' . self::$QUIZ_TITLE . '" required />
					<p>
						<input type="submit" value="Skapa"/>
					</p>
				  </form>';

        return $html;
    }

	// Visa formulär för skapande av fråga
    public function getQuestionFormHTML($questionNumber) {

        $html = '<h3><a href="' . \Settings::$ROOT . '">Meny</a></h3>
			    <h2>Skapa fråga ' . $questionNumber . '</h2>';
				
		if($this->message->hasMessage()) {
			$html .= '<p class="message">' . $this->message->getMessage() . '</p>';
		}
				
		$html .= '<form id="questionForm" method="POST" enctype="multipart/form-data">
					<label for="question">Ange fråga</label>
						<input type="text" name="' . self::$QUESTION . '" id="question" required ';
					
		if(isset($_POST[self::$QUESTION]))
			$html .= 'value="' . $_POST[self::$QUESTION] . '" />';
		else
			$html .= '/>';
										
		$html .= '<label for="' . self::$ANSWER_1 . '">Ange svar</label>
					<input type="text" name="' . self::$ANSWER_1 . '"  id="' . self::$ANSWER_1 . '" required ';
					
		if(isset($_POST[self::$ANSWER_1]))
			$html .= 'value="' . $_POST[self::$ANSWER_1] . '" />';
		else
			$html .= '/>';		
										
		$html .= '<label for="' . self::$ANSWER_2 . '"> Ange svarsalternativ 1</label>
					<input type="text" name="' . self::$ANSWER_2 . '" id="' . self::$ANSWER_2 . '" required  ';
					
		if(isset($_POST[self::$ANSWER_2]))
			$html .= 'value="' . $_POST[self::$ANSWER_2] . '" />';
		else
			$html .= '/>';	
					
		$html .= '<label for="' . self::$ANSWER_3 . '"> Ange svarsalternativ 2</label>
					<input type="text" name="' . self::$ANSWER_3 . '" id="' . self::$ANSWER_3 . '" required  ';
					
		if(isset($_POST[self::$ANSWER_3]))
			$html .= 'value="' . $_POST[self::$ANSWER_3] . '" />';
		else
			$html .= '/>';
					
		$html .= '<label for="' . self::$ANSWER_4 . '"> Ange svarsalternativ 3</label>
					<input type="text" name="' . self::$ANSWER_4 . '" id="' . self::$ANSWER_4 . '" required  ';
					
		if(isset($_POST[self::$ANSWER_4]))
			$html .= 'value="' . $_POST[self::$ANSWER_4] . '" />';
		else
			$html .= '/>';			
					
		$html .= '<label for="file"> Välj eventuell bild</label>
					<input id="file" type="file" name="' . self::$NEW_FILE . '">';

		$html .= '<p><input type="submit" value="Spara"/></p>
			</form>';

		$html .= '<p>En Quiz måste innehålla minst 3 frågor för att kunna aktiveras. Aktivera ditt Quiz 
					<a href="?' . QuizzyMaster::$ACTION . '=' . QuizzyMaster::$MANAGE_QUIZ . '"> här</a>.
				  </p>';
			
        return $html;
    }

    // Lista Administratörens Quiz samt möjligheten att utvidga eller aktivera/deaktivera ett quiz
    public function getAdminQuizListHTML($quizes) {

        $html = '<h3><a href="' . \Settings::$ROOT . '">Meny</a></h3>
				 <h2>Mina Quiz</h2>';

        if ($this->messageHandler->hasMessage())
            $html .= '<p>' . $this->messageHandler->getMessage() . '</p>';

        $html .= '<table class="listtable">
                      <thead>
                        <tr>
                          <th class="left">Quiznamn</th>
                          <th class="center">Storlek</th>
                          <th class="center">Utöka</th>
                          <th class="center">Status</th>
                        </tr>
                      </thead>
                      <tbody>';        

        foreach ($quizes as $quiz) {

            $html .= '<tr>
                        <td class="left">
							<a href="?' . \view\QuizzyMaster::$ACTION . '=' . \view\QuizzyMaster::$QUIZ_STATS 
							. '&' . \view\QuizzyMaster::$QUIZ_ID . '=' . $quiz->getQuizId() . '">' . $quiz->getQuizName() . '</a>
						</td>
					    <td class="center">
							' . count($quiz->getQuestions()) . '
						</td>
                        <td class="center">
							<a href="?' . \view\QuizzyMaster::$ACTION . '=' . \view\QuizzyMaster::$CREATE_QUIZ 
							. '&' . \view\QuizzyMaster::$QUIZ_ID . '=' . $quiz->getQuizId() . '">+</a>
						</td>';

            if ($quiz->getIsActive())
                $html .= '<td class="center"><a href="?' . \view\QuizzyMaster::$ACTION . '=' . \view\QuizzyMaster::$DEACTIVATE_QUIZ 
						 . '&' . \view\QuizzyMaster::$QUIZ_ID . '=' . $quiz->getQuizId() . '">Inaktivera</a></td>';
            elseif(count($quiz->getQuestions()) >=  self::$QUIZ_MIN_SIZE)
                $html .= '<td class="center"><a href="?' . \view\QuizzyMaster::$ACTION . '=' . \view\QuizzyMaster::$ACTIVATE_QUIZ 
						  . '&' . \view\QuizzyMaster::$QUIZ_ID . '=' . $quiz->getQuizId() . '">Aktivera</a></td>';

            $html .= '</tr>';
        }

        $html .= '  </tbody>
                  </table>';
				  
		$html .= '<p>En Quiz måste innehålla minst ' . self::$QUIZ_MIN_SIZE . ' frågor för att kunna aktiveras.</p>';

        return $html;
    }

    // Visa statistik för ett quiz
    public function getQuizStatisticsHTML($quiz, $resultArray) {
        
        $html = '<h3><a href="' . \Settings::$ROOT . '">Meny</a></h3>
				 <h2>Quiz Statistik - ' . $quiz->getQuizname() . '</h2>';

        $html .= '<h3>Antal frågor: ' . count($quiz->getQuestions()) . '</h3>';


        $html .= '<table class="listtable">
                      <thead>
                        <tr>
                          <th class="left">Användare</th>
                          <th class="center">Antal rätt</th>
                        </tr>
                      </thead>
                      <tbody>';

        foreach ($resultArray as $result) {

            $html .= '<tr>
                        <td class="left">' . $result[1] . '
                        <td class="center">' . $result[2] . '
                     </tr>';
        }

        $html .= '</tbody>
                  </table>';
    
        return $html;
    }
    
    // Visa statistik för en användare
    public function getUserStatisticsHTML($user, $userResultArray) {

        $html = '<h3><a href="' . \Settings::$ROOT . '">Meny</a></h3>
				 <h2>Statistik - ' . $user->getUsername() . '</h2>';

        $html .= '<h3>Antal quiz: ' . count($userResultArray) . '</h3>';

        $html .= '<table class="listtable">
                      <thead>
                        <tr>
                          <th class="left">Quiznamn</th>
                          <th class="center">Resultat</th>
                        </tr>
                      </thead>
                      <tbody>';

        foreach ($userResultArray as $result) {

            $html .= '<tr>
                        <td class="left">' . $result[0] . '
                        <td class="center">' . $result[1] . ' / ' . $result[2] . '
                     </tr>';
        }

        $html .= '</tbody>
                  </table>';
    
        return $html;
    }

    // Lista tillgängliga quiz
    public function getAvalibleQuizListHTML($quizes) {

        $html = '<h3><a href="' . \Settings::$ROOT . '">Meny</a></h3>
				 <h2>Tillgängliga Quiz</h2>';

        $html .= '<table class="listtable">
                      <thead>
                        <tr>
                          <th class="left">Quiznamn</th>
                          <th class="center">Antal frågor</th>
                        </tr>
                      </thead>
                      <tbody>';

        foreach ($quizes as $quiz) {
            $html .= '<tr>
                        <td class="left">
							<a href="?' . \view\QuizzyMaster::$ACTION . '=' . \view\QuizzyMaster::$DO_QUIZ 
							. '&' . \view\QuizzyMaster::$QUIZ_ID . '=' . $quiz->getQuizId() . '">' . $quiz->getQuizName() . '</a>
						</td>
                        <td class="center">
							' . count($quiz->getQuestions()) . '
						</td>
                      </tr>';
        }

        $html .= '  </tbody>
                  </table>';

        return $html;
    }

    // List genomförda quiz
    public function getDoneQuizListHTML($quizes, $userId, $quizDAL) {

        $html = '<h3><a href="' . \Settings::$ROOT . '">Meny</a></h3>
				 <h2>Gjorda Quiz</h2>';

        $html .= '<table class="listtable">
                      <thead>
                        <tr>
                          <th class="left">Quiznamn</th>
                          <th class="center">Resultat</th>
                        </tr>
                      </thead>
                      <tbody>';

        foreach ($quizes as $quiz) {

            $html .= '<tr>
                        <td class="left">
							<a href="?' . \view\QuizzyMaster::$ACTION . '=' . \view\QuizzyMaster::$SHOW_RESULT 
							. '&' . \view\QuizzyMaster::$QUIZ_ID . '=' . $quiz->getQuizId() . '">' . $quiz->getQuizName() . '</a>
						</td>
                        <td class="center">
							' . $quizDAL->getQuizResult($quiz->getQuizId(), $userId) . ' av ' . count($quiz->getQuestions()) . '
						</td>
                      </tr>';
        }

        $html .= '  </tbody>
                  </table>';
        
        return $html;
    }

    // Visa resultatet för ett quiz
    public function getQuizResultHTML($quiz, $userAnswers, $result) {

        $html = '<h3><a href="' . \Settings::$ROOT . '">Meny</a></h3>
				 <h2>Resultat - ' . $quiz->getQuizName() . '</h2>';

        $html .= '<h3>Antal rätt: ' . $result . ' / ' . count($quiz->getQuestions()) . '</h3>';

        foreach ($quiz->getQuestions() as $question) {

            $html .= '<div class="result">
                    <h3>' . $question->getQuestion() . '</h4>';

            $html .= '<p>Rätt svar: ' . $question->getCorrectAnswer() . '</p>
					  <p>Ditt svar: ' . $userAnswers[$question->getQuestionId()] . '</p></div>';
        }

        return $html;
    }
}