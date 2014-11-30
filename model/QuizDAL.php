<?php

namespace model;

require_once('model/SuperDAL.php');
require_once("model/Quiz.php");
require_once('model/QuestionDAL.php');

class QuizDAL extends \model\SuperDAL {

    public function getUserResultArray($userId) {

		try {
			$this->connectToDB();
		 
			$sql = 'SELECT ' . self::$quiz_tableName . '.' . self::$quiz_nameField . ', 
						(SELECT COUNT(*)
						FROM ' . self::$done_tableName . '
						INNER JOIN ' . self::$userAnswer_tableName . '
						ON ' . self::$done_tableName . '.' . self::$done_doneQuizIdField . '=' .  self::$userAnswer_tableName . '.' . self::$userAnswer_doneQuizIdField . '
						INNER JOIN ' . self::$answer_tableName . '
						ON ' . self::$userAnswer_tableName . '.' . self::$userAnswer_answerIdField . '=' . self::$answer_tableName . '.' . self::$answer_idField . '
						WHERE ' . self::$done_tableName . '.' . self::$done_quizIdField . '=' . self::$quiz_tableName . '.' . self::$quiz_idField . ' AND ' . self::$done_tableName . '.' . self::$done_userIdField . '=:user_Id AND ' . self::$answer_isCorrectField. '=true) AS result,
						
						(SELECT COUNT(*)
						FROM ' . self::$question_tableName . '
						WHERE ' . self::$question_tableName . '.' . self::$question_quizIdField . '=' . self::$done_tableName . '.' . self::$done_quizIdField . ') AS quizSize

					FROM ' . self::$done_tableName . '
					INNER JOIN ' . self::$quiz_tableName . '
					ON ' . self::$quiz_tableName . '.' . self::$quiz_idField . '=' . self::$done_tableName . '.' . self::$done_quizIdField . '
					WHERE ' . self::$done_tableName . '.' . self::$done_userIdField . '=:user_Id AND ' . self::$done_isCompleteField . '=true';

			$stmt = $this->dbConnection->prepare($sql);

			$stmt->execute(array('user_Id' => $userId));

			$userResult = $stmt->fetchAll(\PDO::FETCH_NUM);

			return $userResult;
		
		} catch(\Exception $e) {
			die($e->getMessage());
		}
    }

    public function getQuizResultArray($quizId) {
		
		try {
			$this->connectToDB();

			$result = 'result';

			$sql = 'SELECT ' . self::$tableName . '.' . self::$userIdField . ', ' . self::$usernameField . ', (SELECT COUNT(*) 
									  FROM ' . self::$done_tableName . '
									  INNER JOIN ' . self::$userAnswer_tableName . '
									  ON ' . self::$done_tableName . '.' . self::$done_doneQuizIdField . '=' . self::$userAnswer_tableName . '.' . self::$userAnswer_doneQuizIdField . '
									  INNER JOIN ' . self::$answer_tableName . '
									  ON ' . self::$userAnswer_tableName . '.' . self::$userAnswer_answerIdField . '=' . self::$answer_tableName . '.' . self::$answer_idField . '
									  WHERE ' . self::$done_quizIdField . '=:quiz_Id AND ' . self::$tableName . '.' . self::$userIdField . '=' . self::$done_tableName . '.' . self::$done_userIdField . ' AND ' . self::$answer_isCorrectField . '=true) AS ' . $result . '
					FROM ' . self::$tableName . '
					INNER JOIN ' . self::$done_tableName . '
					ON ' . self::$tableName . '.' . self::$userIdField . '=' . self::$done_tableName . '.' . self::$done_userIdField . '
					WHERE ' . self::$done_quizIdField . '=:quiz_Id AND ' . self::$done_isCompleteField . '=true ORDER BY ' . $result . ' DESC';

			$stmt = $this->dbConnection->prepare($sql);

			$stmt->execute(array('quiz_Id' => $quizId));

			$quizResult = $stmt->fetchAll(\PDO::FETCH_NUM);

			return $quizResult;
		
		} catch(\Exception $e) {
			die($e->getMessage());
		}
    }

    public function getQuizResult($quizId, $userId) {
		
		try {
			$this->connectToDB();

			$sql = 'SELECT COUNT(' . self::$answer_isCorrectField . ')
					FROM ' . self::$done_tableName . '
					INNER JOIN ' . self::$userAnswer_tableName . '
					ON ' . self::$done_tableName . '.' . self::$done_doneQuizIdField . '=' . self::$userAnswer_tableName . '.' . self::$userAnswer_doneQuizIdField . '
					INNER JOIN ' . self::$answer_tableName . '
					ON ' . self::$userAnswer_tableName . '.' . self::$userAnswer_answerIdField. '=' . self::$answer_tableName . '.' . self::$answer_idField . '
					WHERE ' . self::$done_quizIdField. '=:quiz_Id AND ' . self::$done_userIdField . '=:user_Id AND ' . self::$answer_isCorrectField . '=true';

			$stmt = $this->dbConnection->prepare($sql);

			$stmt->execute(array('user_Id' => $userId,
								 'quiz_Id' => $quizId)
			);

			$quizResult = $stmt->fetch();

			return $quizResult[0];
		
		} catch(\Exception $e) {
			die($e->getMessage());
		}
    }

    public function toogleQuizActivation($quizId) {

		try {
			$this->connectToDB();

			$sql = 'SELECT ' . self::$quiz_isActiveField . '
					FROM ' . self::$quiz_tableName . '
					WHERE ' . self::$quiz_idField . '=:quiz_Id';

			$stmt = $this->dbConnection->prepare($sql);

			$stmt->execute(array('quiz_Id' => $quizId));

			$quizIsActive = $stmt->fetch();

			if ($quizIsActive[0])
				$status = false;
			else
				$status = true;

			$sql = 'UPDATE ' . self::$quiz_tableName . '
					SET ' . self::$quiz_isActiveField . '=:status
					WHERE ' . self::$quiz_idField . '=:quiz_Id';

			$secondStmt = $this->dbConnection->prepare($sql);

			$secondStmt->execute(array('status' => $status,
				'quiz_Id' => $quizId)
			);
		
		} catch(\Exception $e) {
			die($e->getMessage());
		}
    }

    public function updateDoneQuizIsComplete($doneQuizId) {

		try {
			$this->connectToDB();

			$sql = 'UPDATE ' . self::$done_tableName . '
					SET ' . self::$done_isCompleteField . '=TRUE
					WHERE ' . self::$done_doneQuizIdField . '=:doneQuiz_Id';

			$stmt = $this->dbConnection->prepare($sql);

			$stmt->execute(array('doneQuiz_Id' => $doneQuizId));
		
		} catch(\Exception $e) {
			die($e->getMessage());
		}
    }

    public function saveDoneQuiz($userId, $quizId) {

		try {
			$this->connectToDB();

			$sql = 'INSERT INTO ' . self::$done_tableName . ' (' . self::$done_userIdField . ', ' . self::$done_quizIdField . ') 
					VALUES (:user_Id, :quiz_Id)';

			$stmt = $this->dbConnection->prepare($sql);

			$stmt->execute(array('user_Id' => $userId,
				'quiz_Id' => $quizId)
			);

			$doneQuizId = $this->dbConnection->lastInsertId();

			return $doneQuizId;
		
		} catch(\Exception $e) {
			die($e->getMessage());
		}
    }

    public function getDoneQuizId($userId, $quizId) {

		try {
			$this->connectToDB();

			$sql = 'SELECT ' . self::$done_doneQuizIdField . '
					FROM ' . self::$done_tableName . '
					WHERE ' . self::$done_userIdField . '=:user_Id AND ' . self::$done_quizIdField . '=:quiz_Id';

			$stmt = $this->dbConnection->prepare($sql);

			$stmt->execute(array('user_Id' => $userId,
				'quiz_Id' => $quizId)
			);

			$doneQuizId = $stmt->fetch();

			return $doneQuizId[0];
		
		} catch(\Exception $e) {
			die($e->getMessage());
		}
    }

    public function getUserAnswersArray($userId, $quizId) {

		try {
			$this->connectToDB();

			$sql = 'SELECT ' . self::$answer_questionIdField . ', ' . self::$answer_answerField . '
					FROM ' . self::$answer_tableName . ' 
					INNER JOIN ' . self::$userAnswer_tableName . ' 
							ON ' . self::$answer_tableName . '.' . self::$answer_idField . '=' . self::$userAnswer_tableName . '.' . self::$userAnswer_answerIdField . ' 
					INNER JOIN ' . self::$done_tableName . ' 
							ON ' . self::$done_tableName . '.' . self::$done_doneQuizIdField . '=' . self::$userAnswer_tableName . '.' . self::$userAnswer_doneQuizIdField . '
					WHERE ' . self::$done_userIdField . '=:user_Id AND ' . self::$done_quizIdField . '=:quiz_Id';

			$stmt = $this->dbConnection->prepare($sql);

			$stmt->execute(array('user_Id' => $userId, 'quiz_Id' => $quizId));

			$answersArray = array();

			while ($row = $stmt->fetch()) {
				$answersArray[$row[self::$answer_questionIdField]] = $row[self::$answer_answerField];
			}

			return $answersArray;
		
		} catch(\Exception $e) {
			die($e->getMessage());
		}
    }

    public function getEmptyDoneQuizes($userId) {

		try {
			$this->connectToDB();

			$sql = 'SELECT ' . self::$quiz_tableName . '.' . self::$quiz_idField . ', ' . self::$quiz_creatorIdField . ', ' . self::$quiz_nameField . ', ' . self::$quiz_isActiveField . '
					FROM ' . self::$quiz_tableName . '
					INNER JOIN ' . self::$done_tableName . '
					ON ' . self::$done_tableName . '.' . self::$done_quizIdField . '=' . self::$quiz_tableName . '.' . self::$quiz_idField . '
					WHERE ' . self::$done_userIdField . '=:user_Id AND ' . self::$done_isCompleteField . '=TRUE';

			$stmt = $this->dbConnection->prepare($sql);

			$stmt->execute(array('user_Id' => $userId));

			$quizes = array();

			while ($row = $stmt->fetch()) {
				$quiz = new \model\Quiz($row[self::$quiz_nameField], $row[self::$quiz_creatorIdField], $row[self::$quiz_isActiveField]);
				$quiz->setQuizId($row[self::$quiz_idField]);
				$quizes[] = $quiz;
			}

			return $quizes;
		
		} catch(\Exception $e) {
			die($e->getMessage());
		}
    }

    public function getDoneQuizes($userId) {

		try {
			$this->connectToDB();

			$sql = 'SELECT ' . self::$quiz_tableName . '.' . self::$quiz_idField . ', ' . self::$quiz_creatorIdField . ', ' . self::$quiz_nameField . ', ' . self::$quiz_isActiveField . '
					FROM ' . self::$quiz_tableName . '
					INNER JOIN ' . self::$done_tableName . '
					ON ' . self::$done_tableName . '.' . self::$done_quizIdField . '=' . self::$quiz_tableName . '.' . self::$quiz_idField . '
					WHERE ' . self::$done_userIdField . '=:user_Id AND ' . self::$done_isCompleteField . '=TRUE';

			$stmt = $this->dbConnection->prepare($sql);

			$stmt->execute(array('user_Id' => $userId));

			$quizes = array();
			$questionDAL = new \model\QuestionDAL();

			while ($row = $stmt->fetch()) {
				$quiz = new \model\Quiz($row[self::$quiz_nameField], $row[self::$quiz_creatorIdField], $row[self::$quiz_isActiveField]);
				$quiz->setQuizId($row[self::$quiz_idField]);

				// Populerar Quiz med Questions
				$questionDAL->populateQuizObject($quiz);

				$quizes[] = $quiz;
			}

			return $quizes;
		
		} catch(\Exception $e) {
			die($e->getMessage());
		}
    }

    public function getAdminQuizes($userId) {
		
		try {
			$this->connectToDB();

			$sql = 'SELECT *
					FROM ' . self::$quiz_tableName . '
					WHERE ' . self::$quiz_creatorIdField . '=:user_Id';

			$stmt = $this->dbConnection->prepare($sql);

			$stmt->execute(array('user_Id' => $userId));

			$quizes = array();
			$questionDAL = new \model\QuestionDAL();

			while ($row = $stmt->fetch()) {
				$quiz = new \model\Quiz($row[self::$quiz_nameField], $row[self::$quiz_creatorIdField], $row[self::$quiz_isActiveField]);
				$quiz->setQuizId($row[self::$quiz_idField]);

				// Populerar Quiz med Questions
				$questionDAL->populateQuizObject($quiz);

				$quizes[] = $quiz;
			}

			return $quizes;
		
		} catch(\Exception $e) {
			die($e->getMessage());
		}
    }

    public function getQuizById($quizId) {

		try {
			$this->connectToDB();

			$sql = 'SELECT *
					FROM ' . self::$quiz_tableName . '
					WHERE ' . self::$quiz_idField . ' = :quiz_Id';

			$stmt = $this->dbConnection->prepare($sql);

			$stmt->execute(array('quiz_Id' => $quizId));

			$result = $stmt->fetch();

			// Skapar Quiz med titel och id
			$quiz = new \model\Quiz($result[self::$quiz_nameField], $result[self::$quiz_creatorIdField], $result[self::$quiz_isActiveField]);
			$quiz->setQuizId($result[self::$quiz_idField]);

			// Populerar Quiz med Questions
			$questionDAL = new \model\QuestionDAL();
			$questionDAL->populateQuizObject($quiz);

			return $quiz;
		
		} catch(\Exception $e) {
			die($e->getMessage());
		}
    }

    public function saveQuiz(\model\Quiz $quiz) {

		try {
			$this->connectToDB();

			$sql = 'INSERT INTO ' . self::$quiz_tableName . ' (' . self::$quiz_creatorIdField . ', ' . self::$quiz_nameField . ', ' . self::$quiz_isActiveField . ') 
					VALUES (:creator_Id, :quiz_Name, :is_Active)';

			$stmt = $this->dbConnection->prepare($sql);

			$stmt->execute(array('creator_Id' => $quiz->getCreatorId(), 'quiz_Name' => $quiz->getQuizName(), 'is_Active' => $quiz->getIsActive()));

			$quizId = $this->dbConnection->lastInsertId();

			$questions = $quiz->getQuestions();

			$questionDAL = new \model\QuestionDAL();

			foreach ($questions as $question)
				$questionDAL->saveQuestionByQuizId($question, $quizId);

			return $quizId;
		
		} catch(\Exception $e) {
			die($e->getMessage());
		}
    }

    public function getAvalibleQuizes($userId) {

		try {
			$this->connectToDB();

			$sql = 'SELECT *
					FROM ' . self::$quiz_tableName . '
					WHERE NOT EXISTS
							(SELECT * 
					FROM ' . self::$done_tableName . '
					WHERE ' . self::$done_tableName . '.' . self::$done_quizIdField . '=' . self::$quiz_tableName . '.' . self::$quiz_idField . '
					AND ' . self::$done_tableName . '.' . self::$done_userIdField . '= :user_Id AND ' . self::$done_isCompleteField . '=TRUE)
					AND ' . self::$quiz_isActiveField . '=TRUE';

			$stmt = $this->dbConnection->prepare($sql);

			$stmt->execute(array('user_Id' => $userId));

			$quizes = array();
			$questionDAL = new \model\QuestionDAL();

			while ($row = $stmt->fetch()) {
				$quiz = new \model\Quiz($row[self::$quiz_nameField], $row[self::$quiz_creatorIdField], $row[self::$quiz_isActiveField]);
				$quiz->setQuizId($row[self::$quiz_idField]);

				// Populerar Quiz med Questions
				$questionDAL->populateQuizObject($quiz);

				$quizes[] = $quiz;
			}

			return $quizes;
		
		} catch(\Exception $e) {
			die($e->getMessage());
		}
    }
}