<?php


/**
 * Základní třída modelu.
 */
class Model extends Nette\Object
{
	/** @var Nette\Database\Connection */
	public $database;


	/**
	 * @param Nette\Database\Connection $database
	 */
	public function __construct(Nette\Database\Connection $database)
	{
		$this->database = $database;
	}


	/**
	 * Získá tabulku úkolů.
	 * @return Nette\Database\Table\Selection
	 */
	public function getTasks()
	{
		return $this->database->table('task');
	}

	/**
	 * Najde záznam
	 * @return Nette\Database\Table\ActiveRow OR NULL
	 */
	public function getTask($id)
	{
		return $this->tasks->get($id);
	}

	/**
	 * Získá tabulku se seznamy úkolů.
	 * @return Nette\Database\Table\Selection
	 */
	public function getTaskLists()
	{
		return $this->database->table('tasklist');
	}

	/**
	 * Získá tabulku uživatelů.
	 * @return Nette\Database\Table\Selection
	 */
	public function getUsers()
	{
		return $this->database->table('user');
	}

}
