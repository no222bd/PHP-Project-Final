<?php

namespace model;

require_once('model/SuperDAL.php');

class QuestionDAL extends \model\SuperDAL {

	public function saveUserAnswer($doneQuizId, $answerId) {

		try {
			$this->connectToDB();

			$sql = 'INSERT INTO ' . self::$userAnswer_tableName . ' (' .self::$userAnswer_doneQuizIdField . ', ' . self::$userAnswer_answerIdField . ') 
					VALUES (:doneQuiz_Id, :answer_Id)';

			$stmt = $this->dbConnection->prepare($sql);

			$stmt->execute(array(
				'doneQuiz_Id' => $doneQuizId,
				'answer_Id' => $answerId)
			);
		
		} catch(\Exception $e) {
			die($e->getMessage());
		}
	}

	public function getAnswerIdByQuestionIdAndAnswer($questionId, $answer) {

		try {
			$this->connectToDB();

			$sql = 'SELECT ' . self::$answer_idField . '
					FROM ' . self::$answer_tableName . '
					WHERE ' . self::$answer_questionIdField . '=:question_Id AND ' . self::$answer_answerField . '=:answer';

			$stmt = $this->dbConnection->prepare($sql);

			$stmt->execute(array('question_Id' => $questionId,
								 'answer' => $answer)
			);
		
			$answerId = $stmt->fetch();

			return $answerId[0];
		
		} catch(\Exception $e) {
			die($e->getMessage());
		}
	}

	public function populateQuizObject($quiz) {

		try {
			$this->connectToDB();

			$sql = 'SELECT *
					FROM ' . self::$question_tableName . '
					WHERE ' . self::$question_quizIdField . ' = :quiz_Id';

			$stmt = $this->dbConnection->prepare($sql);
		
			$stmt->execute(array('quiz_Id' => $quiz->getQuizId()));

			while($row = $stmt->fetch()) {
				$answers = $this->getAnswersByQuestionId($row[self::$question_idField]);
				$question = new \model\Question($row[self::$question_questionField], $answers);
				$question->setQuestionId($row[self::$question_idField]);

				$question->setMediaPath($this->getMediaPath($row[self::$question_idField]));

				$quiz->addQuestion($question);
			}
		
		} catch(\Exception $e) {
			die($e->getMessage());
		}
	}

	public function getMediaPath($questionId) {
		
		try {
			$sql = 'SELECT *
					FROM ' . self::$mediaPath_tableName . '
					WHERE ' . self::$mediaPath_questionIdField . ' = :question_Id';

			$stmt = $this->dbConnection->prepare($sql);
		
			$stmt->execute(array('question_Id' => $questionId));
			
			$result = $stmt->fetch();

			if($result)
						return $result[self::$mediaPath_path];
					
		} catch(\Exception $e) {
			die($e->getMessage());
		}
	}

	private function getAnswersByQuestionId($questionId) {

		try {
			$this->connectToDB();

			$sql = 'SELECT * FROM ' . self::$answer_tableName . '
					WHERE ' . self::$answer_questionIdField . ' = :question_Id
					ORDER BY ' . self::$answer_isCorrectField . ' DESC';

			$stmt = $this->dbConnection->prepare($sql);

			$stmt->execute(array('question_Id' => $questionId));
		
			$answersArray = array();

			while($row = $stmt->fetch()) {
				$answersArray[] = $row[self::$answer_answerField];
			}

			return $answersArray;
		
		} catch(\Exception $e) {
			die($e->getMessage());
		}
	}

	public function saveQuestionByQuizId(\model\Question $question, $quizId) {
		
		try {	
			$this->connectToDB();

			$sql = 'INSERT INTO ' . self::$question_tableName . ' (' . self::$question_quizIdField . ', ' . self::$question_questionField . ') 
					VALUES (:quiz_Id, :question)';

			$stmt = $this->dbConnection->prepare($sql);
		
			$stmt->execute(array(
					'quiz_Id' => $quizId,
					'question' => $question->getQuestion())
				);

			$questionId = $this->dbConnection->lastInsertId();

			if(!is_null($question->getMediaPath()))
				$this->saveMediaPath($questionId, $question->getMediaPath());

			$answers = $question->getAnswers();
			$numberOfAnswers = count($answers);

			
			for($i = 0; $i < $numberOfAnswers; $i++) {

				if($i == 0)
					$isCorrect = TRUE;
				else
					$isCorrect = FALSE;

				$this->saveAnswer($questionId, $answers[$i], $isCorrect);
			}
		
		} catch(\Exception $e) {
			die($e->getMessage());
		}
	}

	private function saveMediaPath($questionId, $mediaPath) {

		try {
			$this->connectToDB();

			$sql = 'INSERT INTO ' . self::$mediaPath_tableName . ' (' .self::$mediaPath_questionIdField . ', ' . self::$mediaPath_path . ')
					VALUES (:question_Id, :media_Path)';

			$stmt = $this->dbConnection->prepare($sql);

			$stmt->execute(array(
				'question_Id' => $questionId,
				'media_Path' => $mediaPath)
			);
		
		} catch(\Exception $e) {
			die($e->getMessage());
		}
	}

	private function saveAnswer($questionId, $answer, $isCorrect) {

		try {
			$this->connectToDB();

			$sql = 'INSERT INTO ' . self::$answer_tableName . ' (' .self::$answer_questionIdField . ', '
								  . self::$answer_answerField . ', ' . self::$answer_isCorrectField . ') 
					VALUES (:question_Id, :answer, :is_Correct)';

			$stmt = $this->dbConnection->prepare($sql);

			$stmt->execute(array(
				'question_Id' => $questionId,
				'answer' => $answer,
				'is_Correct' => $isCorrect)
			);
		
		} catch(\Exception $e) {
			die($e->getMessage());
		}
	}
}