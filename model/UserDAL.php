<?php

namespace model;

require_once('model/SuperDAL.php');
require_once('model/User.php');

class UserDAL extends \model\SuperDAL {

    public function makeAdminById($userId) {

		try {
			$this->connectToDB();

			$sql = 'UPDATE ' . self::$tableName . '
					SET ' . self::$isAdminField . '= TRUE
					WHERE ' . self::$userIdField . '= :user_Id';

			$stmt = $this->dbConnection->prepare($sql);

			$stmt->execute(array('user_Id' => $userId));
		
		} catch(\Exception $e) {
			die($e->getMessage());
		}
    }

    public function deleteUserById($userId) {
		
		try {
			$this->connectToDB();

			$sql = 'DELETE 
					FROM ' . self::$tableName . '
					WHERE ' . self::$userIdField . '= :user_Id';

			$stmt = $this->dbConnection->prepare($sql);

			$stmt->execute(array('user_Id' => $userId));
		
		} catch(\Exception $e) {
			die($e->getMessage());
		}
    }

    public function getUserById($userId) {

		try {
			$this->connectToDB();

			$sql = 'SELECT *
					FROM ' . self::$tableName . '
					WHERE ' . self::$userIdField . ' = :user_Id';

			$stmt = $this->dbConnection->prepare($sql);

			$stmt->execute(array('user_Id' => $userId));

			$result = $stmt->fetch();

			$user = new \model\User($result[self::$usernameField], $result[self::$passwordField], $result[self::$isAdminField]);
			$user->setUserId($result[self::$userIdField]);
			
			return $user;
		
		} catch(\Exception $e) {
			die($e->getMessage());
		}
    }

    public function getUsers() {

		try {
			$this->connectToDB();

			$sql = 'SELECT *
					FROM ' . self::$tableName;

			$stmt = $this->dbConnection->query($sql);

			$users = array();

			while ($row = $stmt->fetch()) {
				$user = new \model\User($row[self::$usernameField], $row[self::$passwordField], $row[self::$isAdminField]);
				$user->setUserId($row[self::$userIdField]);

				$users[] = $user;
			}

			return $users;
		
		} catch(\Exception $e) {
			die($e->getMessage());
		}
    }

    public function getUsersOnly() {
		
		try {
			$this->connectToDB();

			$sql = 'SELECT *
					FROM ' . self::$tableName . '
					WHERE ' . self::$isAdminField . '= :is_Admin';

			$stmt = $this->dbConnection->prepare($sql);

			$stmt->execute(array(
				'is_Admin' => 'FALSE')
			);

			$users = array();

			while ($row = $stmt->fetch()) {
				$user = new \model\User($row[self::$usernameField], $row[self::$passwordField], $row[self::$isAdminField]);
				$user->setUserId($row[self::$userIdField]);

				$users[] = $user;
			}

			return $users;
		
		} catch(\Exception $e) {
			die($e->getMessage());
		}
    }

    public function getArrayOfUsernames() {

		try {
			$this->connectToDB();

			$sql = 'SELECT *
					FROM ' . self::$tableName;

			$stmt = $this->dbConnection->query($sql);

			$usernames = array();

			while ($row = $stmt->fetch()) {
				$usernames[] = $row[self::$usernameField];
			}

			return $usernames;
		
		} catch(\Exception $e) {
			die($e->getMessage());
		}
    }

    public function getUsersDataArray() {

		try {
			$this->connectToDB();

			$sql = 'SELECT *
					FROM ' . self::$tableName;

			$stmt = $this->dbConnection->query($sql);

			$users = array();

			while ($row = $stmt->fetch()) {
				$users[$row[self::$usernameField]] = $row[self::$passwordField];
			}

			return $users;
		
		} catch(\Exception $e) {
			die($e->getMessage());
		}
    }

    public function saveUser(\model\User $user) {
		
		try {
			$this->connectToDB();

			$sql = 'INSERT INTO ' . self::$tableName . ' (' . self::$usernameField . ', ' . self::$passwordField . ', ' . self::$isAdminField . ') 
					VALUES (:username, :password, :is_Admin)';

			$stmt = $this->dbConnection->prepare($sql);

			$stmt->execute(array(
				'username' => $user->getUsername(),
				'password' => $user->getPassword(),
				'is_Admin' => $user->getIsAdmin())
			);
		
		} catch(\Exception $e) {
			die($e->getMessage());
		}
    }
}
