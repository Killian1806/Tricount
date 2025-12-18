<?php

namespace Models;

use Exception;
use PDO;

class User extends Database
{
	private $id;
	private $firstname;
	private $name;
	private $email;
	private $password;
	private $iban;

	public function getFirstname()
	{
		return $this->firstname;
	}

	public function setFirstname($value)
	{
		if (empty($value))
			throw new Exception('Le prénom est requis');
		if (strlen($value) < 3 || strlen($value) > 20)
			throw new Exception('Le prénom doit faire entre 3 et 20 caractères');
		if (!preg_match('/^[a-zA-ZÀ-ÿ]+$/', $value))
			throw new Exception('Le prénom ne peut contenir que des lettres et des lettres accentuées');

		$this->firstname = htmlspecialchars($value);
	}

	public function getName()
	{
		return $this->name;
	}

	public function setName($value)
	{
		if (empty($value))
			throw new Exception('Le nom est requis');
		if (strlen($value) < 3 || strlen($value) > 20)
			throw new Exception('Le nom doit faire entre 3 et 20 caractères');
		if (
			!preg_match('/^[a-zA-ZÀ-ÿ]+$/', $value)
		)
			throw new Exception('Le nom ne peut contenir que des lettres et des lettres accentuées');

		$this->name = htmlspecialchars($value);
	}

	public function getEmail()
	{
		return $this->email;
	}

	public function setEmail($value)
	{
		if (empty($value))
			throw new Exception("L'email est invalide");
		if (!filter_var($value, FILTER_VALIDATE_EMAIL))
			throw new Exception('Adresse mail invalide');

		$this->email = htmlspecialchars($value);
	}

	public function setPassword($value)
	{
		if (empty($value))
			throw new Exception('Le mot de passe est requis');
		if (strlen($value) < 3)
			throw new Exception('Le mot de passe doit au moins faire 3 caractères');

		$this->password = password_hash($value, PASSWORD_DEFAULT);
	}

	public function getPassword()
	{
		return $this->password;
	}

	public function getIban()
	{
		return $this->iban;
	}

	public function setIban($value)
	{
		if (empty($value))
			throw new Exception("L'IBAN est requis");

		$cleanIban = str_replace(' ', '', $value);

		if (strlen($cleanIban) < 15 || strlen($cleanIban) > 34)
			throw new Exception("L'IBAN doit être entre 15 et 34 caractères");
		if (!preg_match('/^[A-Z]{2}[0-9]{2}[A-Z0-9]+$/', $cleanIban))
			throw new Exception("Format d'IBAN invalide");

		$this->iban = htmlspecialchars($value);
	}

	public function register($firstname, $name, $password, $email, $iban)
	{
		$queryExecute = $this->db->prepare("INSERT INTO `users`(`firstname`,`name`,`password`, `email`, `iban`) 
		VALUES (:firstname, :name, :password, :email, :iban)");

		$queryExecute->bindValue(':firstname', $firstname, PDO::PARAM_STR);
		$queryExecute->bindValue(':name', $name, PDO::PARAM_STR);
		$queryExecute->bindValue(':password', $password, PDO::PARAM_STR);
		$queryExecute->bindValue(':email', $email, PDO::PARAM_STR);
		$queryExecute->bindValue(':iban', $iban, PDO::PARAM_STR);

		if ($queryExecute->execute()) {
			return $this->db->lastInsertId();
		}

		return false;
	}

	public function getUserByEmail($email)
	{
		$queryExecute = $this->db->prepare("SELECT `id`, `firstname`, `name`, `email`, `iban`, `password` FROM `users` WHERE `email` = :email");
		$queryExecute->bindValue(':email', $email, PDO::PARAM_STR);
		$queryExecute->execute();

		return $queryExecute->fetch(PDO::FETCH_ASSOC);
	}

	public function deleteUserById($id)
	{
		$queryExecute = $this->db->prepare("DELETE FROM `users` WHERE `id` = :id");
		$queryExecute->bindValue(':id', $id, PDO::PARAM_INT);

		return $queryExecute->execute();
	}

	public function getUserById($id)
	{
		$queryExecute = $this->db->prepare("SELECT `id`, `firstname`, `name`, `password`, `email`, `iban` FROM `users` WHERE `id` = :id");
		$queryExecute->bindValue(':id', $id, PDO::PARAM_INT);
		$queryExecute->execute();

		return $queryExecute->fetch(PDO::FETCH_ASSOC);
	}

	public function updateUser($id, $firstname, $name, $email, $iban)
	{
		$queryExecute = $this->db->prepare("UPDATE `users` SET `firstname` = :firstname, `name` = :name, `email` = :email, `iban` = :iban WHERE `id` = :id");

		$queryExecute->bindValue(':id', $id, PDO::PARAM_INT);
		$queryExecute->bindValue(':firstname', $firstname, PDO::PARAM_STR);
		$queryExecute->bindValue(':name', $name, PDO::PARAM_STR);
		$queryExecute->bindValue(':email', $email, PDO::PARAM_STR);
		$queryExecute->bindValue(':iban', $iban, PDO::PARAM_STR);

		return $queryExecute->execute();
	}

	public function updatePassword($id, $password)
	{
		$queryExecute = $this->db->prepare("UPDATE `users` SET `password` = :password WHERE `id` = :id");

		$queryExecute->bindValue(':id', $id, PDO::PARAM_INT);
		$queryExecute->bindValue(':password', $password, PDO::PARAM_STR);

		return $queryExecute->execute();
	}


}