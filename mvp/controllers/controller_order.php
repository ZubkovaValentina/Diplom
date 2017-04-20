<?php

require_once 'controllers/Controller.php';
require_once 'controllers/controller_client.php';
require_once 'controllers/controller_detail.php';
require_once 'models/Model.php';

class Controller_Order extends Controller
{
	const TYPE_NAME = 'my_order';
	const COL_FULL_NAME = 'key_order';
	
	function __construct()
	{
		parent::__construct(new Model());
	}
	
	/** @Override */
	function all()
	{
		$list_of_items = array(); /* массив объектов Row */

/*
		SELECT o.`key_order`, DATE(o.`date`), c.`key_client`, c.`full_name` FROM `order` AS o 
			LEFT JOIN `client` AS c ON o.key_client=c.key_client;	

*/	
		
		
		$sql = "SELECT o.`".$this->getKeyColumn()."`, DATE(o.`date`) AS `date`, c.`key_client`, c.`full_name` FROM `".$this->getType()."` AS o 
			LEFT JOIN `client` AS c ON o.key_client=c.key_client;";
		
		$this->log->debug("Get model list: ".$sql);
		if($result = $this->db->query($sql))
		{
			/* извлечение индексного массива */
			while($row = $result->fetch_assoc()) $list_of_items[] = $row;
			/* удаление выборки */
			$result->free();
			
			$this->model->__set('list', $list_of_items);
			$this->view->generate('list_order_view.html', null, $this);
		}
		else
		{
			$this->log->error("Ошибка SQL: ".$this->db->error);
			return null;
		}
	}

	/**
	 * @Override
	 */
	function edit()
	{
		$key = $this->getKeyValue();
		/* В запросе указан key — значит это редактирование */
		if($key)
		{
			/* Проверяем GET-запрос — запросы на добавление, удаление деталей в заказ */
			$is_error = $this->processLink($key, 'detail');
			
			/* Проверяем GET-запрос — запросы на добавление, удаление работ в заказ */
			$this->processLink($key, 'service');
			
			/* Проверяем GET-запрос — запросы на привязку сотрудника к накладной */
			$this->processLink($key, 'employee');
			
			$this->model->__set("is_edit", true);
			$sql = "SELECT key_order, DATE(`date`) AS `date`, key_client FROM `".$this->getType()."` WHERE `".$this->getKeyColumn()."`=".$key;
			
			if($result = $this->db->query($sql))
			{
				/* извлечение ассоциативного массива */
				$row = $result->fetch_assoc();
				if($row === null)
				{
					echo "Ошибка! Запись с ключом $key не найдена в таблице «".$this->getType()."»";
					exit(0);
				}
				else $this->model->__set("record", $row);
			
				/* удаление выборки */
				$result->free();
			}
			else
			{
				$this->log->error("Ошибка SQL: ".$this->db->error.' '.$sql);
				return null;
			}
			
			/* Выцепляем из БД список деталей для этого заказа */
			
			$details = array();
			$detail_sum = $this->getOrderLinks($key, 'detail', $details);
			
			$this->model->__set("detail_sum", $detail_sum);
			$this->model->__set("details", $details);

			/* Выцепляем из БД список работ для этого заказа */
			
			$services = array();
			$service_sum = $this->getOrderLinks($key, 'service', $services);
			
			$this->model->__set("service_sum", $service_sum);
			$this->model->__set("services", $services);
			
			/*
			[key_detail => ['kolvo' => kolvo, 'invoice' => invoice]]
			Сюда складируем детали (уникальные. Если деталь уже есть в этом массиве, 
			то увеличиваем kolvo на единицу. Иначе добавляем новую запись.
			*/
			$invoices = array();
			$sql = '
SELECT
	od.id AS id,
	d.key_detail AS key_detail,
	d.name_detail AS name_detail,
	p.key_provider AS key_provider,
	p.name_organization AS name_organization,
	e.key_employee AS key_employee,
	e.full_name AS full_name
FROM order_detail AS od
	LEFT JOIN detail AS d ON d.key_detail=od.key_detail
	LEFT JOIN provider AS p ON p.key_provider=d.key_provider
	LEFT JOIN employee AS e ON e.key_employee=od.key_employee
WHERE od.key_order='.$key;

			if($result = $this->db->query($sql))
			{
				/* извлечение ассоциативного массива */
				while($row = $result->fetch_assoc())
				{
					$key_detail = $row['key_detail'];
					if(isset($invoices[$key_detail])) $invoices[$key_detail]['kolvo']++;
					else
					{
						$invoices[$key_detail] = array();
						$invoices[$key_detail]['kolvo'] = 1;
						$invoices[$key_detail]['invoice'] = $row;
					}
				}
			
				/* удаление выборки */
				$result->free();
			}
			else
			{
				$this->log->error("Ошибка SQL: ".$this->db->error.' '.$sql);
				return null;
			}

			$this->model->__set("invoices", $invoices);
			
			
		}
		else
		{
			$row = array();
			$row['key_order'] = '';
			$row['date'] = '';
			$row['key_client'] = '';
			$this->model->__set("is_edit", false);
		}
		
		$all_clients = $this->getAllLinks('client', 'full_name');
		$all_details =  $this->getAllLinks('detail', 'name_detail');
		$all_services = $this->getAllLinks('service', 'name_service');
		$all_employees = $this->getAllLinks('employee', 'full_name');
		
		$this->model->__set("all_clients", $all_clients);
		$this->model->__set("all_details", $all_details);
		$this->model->__set("all_services", $all_services);
		
		$this->model->__set("all_employees", $all_employees);
		
//		$this->model->__set("is_error", $is_error);
		
		$this->view->generate('order_edit.html', null, $this);
	}
	
	/**
	 * @Override
	 */
	function save()
	{
		$this->model = new Model();
		
		$error = false;
		
		$record = array();
		
		$record['key_order'] = 0;
		$record['key_client'] = 0;
		if(isset($_POST['key_client'])) $record['key_client'] = $_POST['key_client'][0];
		
		if($record['key_client'] == 0) $error = 'Клиент';
		if(empty($_POST['date'])) $record['date'] = date('Y-m-d');
		else $record['date'] = $this->db->real_escape_string($_POST['date']);
		
		$key = $this->getKeyValue();
		$this->model->__set("is_edit", $key);
		
		if(!$error)
		{
			if($key)
			{
				$sql = "UPDATE `".$this->getType()."` SET 
						`key_client`=".$record['key_client'].",
						`date`='".$record['date']."'
						WHERE `".$this->getKeyColumn()."`=$key";
			}
			else
			{
				$sql = "INSERT INTO `".$this->getType()."`(
					`date`, `key_client`) VALUES(
					'".$record['date']."',
					".$record['key_client'].")";
			}
				
			$this->log->debug("update SQL: $sql");
			$this->db->query($sql);
			if($this->db->insert_id > 0) $key = $this->db->insert_id;
			/* Редирект на список текущего типа {my_type} */
			header('Location: /order/edit/?key='.$key);
			exit(0);
		}
		$this->model->__set('error', $error);
		
		$record['key_order'] = $key;
		
		$all_clients = $this->getAllLinks('client', 'full_name');
		$this->model->__set("all_clients", $all_clients);
		$this->model->__set('record', $record);
		
		$this->view->generate('order_edit.html', null, $this);
	}
	
	/**
	 * $key - номер заказа
	 * $link - таблица для связи (detail или service)
	 */
	function processLink($key, $link)
	{
		$key_link = 0;
		if(isset($_GET[$link]))
		{
			/* Привязка сотрудника к накладной */
			if($link === 'employee')
			{
				foreach(array_keys($_GET[$link]) as $key_detail)
				{
					$key_employee = $_GET[$link][$key_detail];
					if($key_employee == 0) $key_employee = 'NULL';
					$sql = "
UPDATE order_detail SET key_employee=$key_employee WHERE 
	key_detail=$key_detail AND 
	key_order=".$key;
					if(!$this->db->query($sql))
					{
						$this->log->error("Ошибка SQL: ".$this->db->error.' '.$sql);
					}
				}
				header('Location: /order/edit/?key='.$key);
				exit(0);
			}
			else if($_GET[$link][0] != 0)
			{
				$key_link = $_GET[$link][0];
				$sign = '-';
				if($_GET['action'] == 'add') $sql = "
INSERT INTO order_$link(key_order, key_$link) VALUES(".$key.", ".$key_link.")";
			}
		}
		else if(isset($_GET["del_$link"]) and $_GET["del_$link"] != 0)
		{
			$key_link = $_GET["del_$link"];
			$sign = '+';
			
			
			$removed_key_detail = 0;
			if($link === 'detail')
			{
				/* Предварительно узнаем key_detail чтобы потом вернуть ее на склад */
				$sql =
"SELECT key_detail FROM order_detail WHERE id=$key_link";
				if($result = $this->db->query($sql))
				{
					while($row = $result->fetch_assoc())
					{
						$removed_key_detail = $row['key_detail'];
						break;
					}
					/* удаление выборки */
					$result->free();
				}
			}
			
			$sql = "
DELETE FROM order_$link WHERE id=".$key_link;
			$key_link = $removed_key_detail;
		}

		if(isset($sql) and !$this->db->query($sql))
		{
			$this->log->error("Ошибка SQL: ".$this->db->error.' '.$sql);
			exit(0);
		}
		
		/* Добавляем или удаляем со склада деталь (уменьшаем или увеличиваем на 
		единицу поле «kolvo» ) */
		if($link === 'detail' and isset($sign))
		{
			/* Перед удалением проверяем оставшееся кол-во и, если 0, то вводим сообщение о пустом складе */
			if($sign === '-')
			{
				$sql = "SELECT kolvo, name_detail FROM detail WHERE key_detail=$key_link";
				$kolvo = 0;
				$name_detail = '';
				if($result = $this->db->query($sql))
				{
					while($row = $result->fetch_assoc())
					{
						$kolvo = $row['kolvo'];
						$name_detail = $row['name_detail'];
						break;
					}
					/* удаление выборки */
					$result->free();
				}
				if($kolvo == 0) return $name_detail;
			}
			
			/*уменьшение детали или увелечение*/
			$sql = "UPDATE detail SET kolvo=kolvo".$sign."1 WHERE key_detail=$key_link";
			//echo $sql;
			//exit(0);
			if(isset($sql) and !$this->db->query($sql))
			{
				$this->log->error("Ошибка SQL: ".$this->db->error.' '.$sql);
				exit(0);
			}
		}
		return false;
	}
	
	/**
	 * Выцепляем все записи из связанной талицы $link 
	 */
	function getAllLinks($link, $name_col)
	{
		$sql_where = '';
		if($link === 'detail') $sql_where = "WHERE kolvo > 0";
		
		$links = array();
		$sql = "
SELECT `key_$link`, `$name_col` FROM `$link` $sql_where ORDER BY `$name_col`";
		if($result = $this->db->query($sql))
		{
			while($row = $result->fetch_assoc()) $links[] = $row;
			/* удаление выборки */
			$result->free();
		}
		else
		{
			$this->log->error("Ошибка SQL: ".$this->db->error);
			$this->log->error("SQL: ".$sql);
			return null;
		}
		
		return $links;
	}
	
	/**
	 * Выцепляем связанные с заказом данные из таблицы $link
	 * $key - номер заказа
	 * $link - название выцепляемой таблицы (detail, service)
	 * $links - массив строк выцепляемой таблицы
	 * возвращает сумму цен.
	 */
	 
	function getOrderLinks($key, $link, &$links)
	{
		$sql = "
SELECT
	od.id AS id,
	o.key_order, 
	d.key_$link AS key_$link, 
	d.name_$link AS name_$link, 
	d.price AS price
			
	FROM order_$link AS od 
		JOIN $link AS d ON d.key_$link=od.key_$link
		JOIN my_order AS o ON o.key_order=od.key_order
	WHERE od.key_order=".$key;
	
		$sum = 0;
		if($result = $this->db->query($sql))
		{
			while($row = $result->fetch_assoc())
			{
				$links[] = $row;
				$sum += $row['price'];
			}
			/* удаление выборки */
			$result->free();
		}
		else
		{
			$this->log->error("Ошибка SQL: ".$this->db->error);
			$this->log->error("SQL: ".$sql);
			return null;
		}
		return $sum;
	}
	
	function getStringRepresentation()
	{
		return self::COL_FULL_NAME;
	}
	
	function getType()
	{
		return self::TYPE_NAME;
	}
	
	/** @Override */
	function getKeyColumn()
	{
		return 'key_order';
	}
	
	/** @Override */
	function getNameColumn()
	{
		return 'key_order';
	}
	
	function getTitle()
	{
		return 'Информация о заказах';
	}
	
	function getHeader()
	{
		return 'Заказы';
	}
}
