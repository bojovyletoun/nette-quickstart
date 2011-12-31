<?php

use Nette\Application\UI;
use Nette\Database\Table\Selection;

/**
 * Komponenta pro výpis seznam úkolů.
 */
class TaskList extends UI\Control
{
	/** @var \Nette\Database\Table\Selection */
	private $tasks;

	/** @var \Model */
	private $model;

	/** @var bool */
	private $displayUser = TRUE;

	/** @var bool */
	private $displayTaskList = FALSE;

	/**
	 * @param Nette\Database\Table\Selection $selection Model, jehož výpis se bude provádět.
	 * @param Model $model
	 */
	public function __construct(Selection $tasks, \Model $model)
	{
		parent::__construct();

		$this->tasks = $tasks;
		$this->model = $model;
	}

	/**
	 * Nastaví, zda se má zobrazovat sloupeček se seznamem úkolů.
	 * @param boolean $displayTaskList
	 */
	public function setDisplayTaskList($displayTaskList)
	{
		$this->displayTaskList = (bool) $displayTaskList;
	}

	/**
	 * Nastaví, zda se má zobrazovat sloupeček s uživatelem, kterému je úkol přiřazen.
	 * @param boolean $displayUser
	 */
	public function setDisplayUser($displayUser)
	{
		$this->displayUser = (bool) $displayUser;
	}

	/**
	 * Vykreslí komponentu. Šablonou komponenty je TaskList.latte.
	 */
	public function render()
	{
		$this->template->setFile(__DIR__ . '/TaskList.latte');
		$this->template->tasks = $this->tasks;
		$this->template->displayUser = $this->displayUser;
		$this->template->displayTaskList = $this->displayTaskList;
		$this->template->userId = $this->presenter->getUser()->getId();
		$this->template->render();
	}

	/**
	 * Signál, který označí zadaný úkol jako splněný.
	 * @param $taskId ID úkolu.
	 */
	public function handleMarkDone($taskId)
	{
		$task = $this->model->getTask($taskId);
		// ověření, zda je tento úkol uživateli skutečně přiřazen
		// $task && je lepší než $task !==NULL &&
		if ($task && $task->user_id == $this->presenter->user->id) {
			$task->done = !$task->done;
			$task->update();
			$state = $task->done ? "splněn" : "nedokončený";
			$this->redrawIfNotAjax(); //pokud není ajax, zprávu nepřidáme
			$this->flashMessage("Úkol je $state.");
		} else {
			$this->flashMessage('Jen vlastník může změnit stav');
			$this->redrawIfNotAjax();
		}
	}

	/**
	 * Tento signál smaže úkol.
	 * @param $taskId ID úkolu.
	 */
	public function handleDelete($taskId)
	{
		$task = $this->model->getTask($taskId);
		if ($task && $task->user_id == $this->presenter->user->id) {
			if (!$task->done) { // odkaz sice není v šabloně, ale  je možné podstrčit url
				$this->flashMessage('nejřív úkol splň', 'error');
				$this->redrawIfNotAjax();
			} else {
				$task->delete();
				$this->redrawIfNotAjax();
				$this->flashMessage(" Záznam $task smazán");
			}
		} else {
			$this->flashMessage('nelze mazat cizí úkoly', 'error');
			$this->redrawIfNotAjax();
		}
	}

	/**
	 * Zkratka pro přesměrování
	 */
	function redrawIfNotAjax()
	{
		if (!$this->presenter->ajax) {
			$this->redirect('this');
		}
		$this->invalidateControl();
	}

}