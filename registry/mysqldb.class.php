<?php
class MySqlDb {
	/*
	 Allow multiple connections
	 each connection is maintained in a variable
	 */
	private $connections = array();
	/*
	 Active Connection to Database
	 */
	private $actionConnection = 0;
	/*
	 Queries have been executed and the results will cached for
	 later, template engine
	 */
	private $queryCache = 0;
	/*
	 * Data which has been prepared and then cached
	 * for later
	 *
	 */
	private $dataCache = array();
	/*
	 *Number of queries made during excution process
	 */
	private $queryCounter = 0;
	/*
	 * Record last query
	 */
	private $last;
	/*
	 * Refence to registry object
	 */
	private $registry;
	/*
	 * Contruct our database object
	 */
	public function __contruct(Registry $registry) {
		$this ->registry = $registry;

	}

	/**
	 * Create new database connection
	 * @param String database hostname
	 * @param String database username
	 * @param String database password
	 * Return new connection
	 */
	public function newConnection($host, $username, $password, $database) {
		$this -> connections[] = new mysqli($host, $username, $password, $database);
		$connection_id = count($this -> connections) - 1;
		if (mysqli_connection_errno()) {
			trigger_error('Error when connection to host. ' . $this -> connections[$connection_id] -> error, E_USER_ERROR);
		}
		return $connection_id;
	}

	/*
	 * Active Connetion */
	public function activeConnection(int $new) {
		$this -> activeConnection = $new;
	}

	/*
	 * Executed query */
	public function executeQuery($queryStr) {
		if (!$result = $this -> connections[$this -> activeConnection] -> query($queryStr)) {
			trigger_error('Error when executing query' . $queryStr . $this -> connections[$this -> activeConnection] -> error, E_USER_ERROR);
		} else {
			$this -> last = $result;
		}

	}

	/*
	 * Get the rows from most recently executed query
	 * Associate array
	 * */
	public function getRows() {
		return $this -> last -> fetch_array(MYSQLI_ASSOC);
	}

	/*
	 * Delete record from database
	 * @param String the table  to remove
	 * @param String the condition which rows to remove
	 * @param int number of rows to be removed
	 * */
	public function deletRecords($table, $condition, $limit) {
		$limit = ($limit == '') ? '' : 'LIMIT' . $limit;
		$delete = "DELETE FROM {$table} WHERE {$condition} {$limit}";
		$this -> executeQuery($delete);
	}

	/*
	 * Update record in the database
	 * @param String the table
	 * @param array of changes field -> value
	 * @param String condition
	 */
	public function updateRecords($table, $changes, $condition) {
		$update = "UPDATE " . $table . "SET ";
		foreach ($changes as $field => $value) {
			$update .= "`" . $field . "`='{$value}',";
		}
		// remove our trailing
		$update = substr($update, 0, -1);
		if ($condition != '') {
			$update .= "WHERE " . $condition;
		}
		$this -> executeQuery($update);
		return true;
	}

	/*
	 * Insert record to database
	 *
	 *
	 */
	public function insertRecords($table, $data) {
		/*
		 * Set up variables for field and value
		 * */
		$fields = 0;
		$values = 0;

		// populated
		foreach ($data as $field => $value) {
			$fields .= "`$field`,";
			$values .= (is_numberic($value) && (intval($value) == $value)) ? $value . "," : "'$value',";
			$fields = substr($fields, 0, -1);
			$values = substr($values, 0, -1);
			$insert = "INSERT INTO $table ({$fields}) VALUES ({$values})";
			// echo insert
			$this -> executeQuery($insert);
			return true;
		}
	}

	/**
	 *  Sanitizing data
	 */
	public function sanitizeData($value) {
		if (get_magic_quotes_gpc()) {
			$value = stripsplashes($value);
		}
		// Quote values
		if (version_compare(phpversion(), "4.3.0") == 1) {
			$value = $this -> connections[$this -> activeConnection] -> escape_string($value);
		} else {
			$value = $this -> connections[$this -> activeConnection] -> real_escape_string($value);
		}
		return $value;

	}

	/*
	 * Get rows from most recently executeQuery
	 */
	public function getRows() {
		return $this -> last -> fetch_array(MYSQLI_ASSOC);
	}

	public function numRows() {
		return $this -> last -> num_rows;
	}

	/*
	 * Return number of affected Rows from previous query
	 */
	public function affectedRows() {
		return $this -> last -> affected_rows;
	}

	/*
	 * Deconstruct the object close all database connection
	 */
	public function __decontruct() {
		foreach ($this->connections as $connection) {
			$connection -> close();
		}
	}

}
?>

