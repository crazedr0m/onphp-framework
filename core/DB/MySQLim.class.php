<?php
/***************************************************************************
 *   Copyright (C) 2007-2008 by Konstantin V. Arkhipov                     *
 *                                                                         *
 *   This program is free software; you can redistribute it and/or modify  *
 *   it under the terms of the GNU Lesser General Public License as        *
 *   published by the Free Software Foundation; either version 3 of the    *
 *   License, or (at your option) any later version.                       *
 *                                                                         *
 ***************************************************************************/

	/**
	 * MySQL DB connector.
	 * 
	 * @see http://www.mysql.com/
	 * @see http://www.php.net/mysqli
	 * 
	 * @ingroup DB
	**/
	class MySQLim extends Sequenceless
	{
		private $needAutoCommit = false;
		private $defaultEngine;
		private $compressed = false;
		private $initCommands = array();

		/**
		 * @param bool $really
		 * @return MySQLim
		 */
		public function setPersistent($really = false)
		{
			$this->persistent = ($really === true);

			return $this;
		}

		/**
		 * @param bool $really
		 * @return MySQLim
		 */
		public function setCompressed($really = false)
		{
			$this->compressed = ($really === true);

			return $this;
		}

		/**
		 * @param string $query
		 * @return MySQLim
		 */
		public function addInitCommand($query)
		{
			$this->initCommands[] = $query;

			return $this;
		}

		/**
		 * @return MySQLim
		**/
		public function setDbEncoding()
		{
			mysqli_set_charset($this->link, $this->encoding);
			
			return $this;
		}

		/**
		 * @param $flag
		 * @return MySQLim
		**/
		public function setNeedAutoCommit($flag)
		{
			$this->needAutoCommit = ($flag == true);
			$this->setupAutoCommit();
			return $this;
		}

		/**
		 * @param string $engine
		 * @return MySQLim
		 */
		public function setDefaultEngine($engine)
		{
			$this->defaultEngine = $engine;
			$this->setupDefaultEngine();
			return $this;
		}

		/**
		 * @return $this
		 * @throws DatabaseException
		 * @throws UnsupportedMethodException
		 */
		public function connect()
		{
			$this->link = mysqli_init();
			$flags = MYSQLI_CLIENT_FOUND_ROWS;
			if ($this->compressed) {
				$flags = $flags | MYSQLI_CLIENT_COMPRESS;
			}

			if (!empty($this->initCommands)) {
				$this->link->options(MYSQLI_INIT_COMMAND, implode(';', $this->initCommands));
			}

			try {
				mysqli_real_connect(
					$this->link,
					($this->persistent ? 'p:': '').$this->hostname,
					$this->username,
					$this->password,
					$this->basename,
					$this->port,
					null,
					$flags
				);
			} catch (BaseException $e) {
				throw new DatabaseException(
					'can not connect to MySQL server: '.$e->getMessage()
				);
			}
			
			if ($this->encoding)
				$this->setDbEncoding();

			$this->setupAutoCommit();
			$this->setupDefaultEngine();
			
			return $this;
		}
		
		/**
		 * @return MySQLim
		**/
		public function disconnect()
		{
			if ($this->isConnected())
				mysqli_close($this->link);

			return $this;
		}
		
		public function isConnected()
		{
			$connected = (parent::isConnected() || $this->link instanceof \mysqli);
			if (!$connected) {
				return false;
			}

			$alive = false;
			try {
				$alive = mysqli_ping($this->link);
			} catch (BaseException $e) {}

			if ($alive) {
				return true;
			}

			if ($connected) {
				try {
					mysqli_close($this->link);
				} catch (BaseException $e) {}
			}
			$this->link = null;

			return false;
		}
		
		/**
		 * Same as query, but returns number of
		 * affected rows in insert/update queries
		**/
		public function queryCount(Query $query)
		{
			$this->queryNull($query);
			
			return mysqli_affected_rows($this->link);
		}
		
		public function queryRow(Query $query)
		{
			$res = $this->query($query);
			
			if ($this->checkSingle($res))
				return mysqli_fetch_assoc($res);
			else
				return null;
		}
		
		public function queryColumn(Query $query)
		{
			$res = $this->query($query);
			
			if ($res) {
				$array = array();

				while ($row = mysqli_fetch_row($res))
					$array[] = $row[0];

				return $array;
			} else
				return null;
		}
		
		public function querySet(Query $query)
		{
			$res = $this->query($query);
			
			if ($res) {
				$array = array();

				while ($row = mysqli_fetch_assoc($res))
					$array[] = $row;

				return $array;
			} else
				return null;
		}
		
		public function queryRaw($queryString)
		{
			if (!$result = mysqli_query($this->link, $queryString)) {
				
				$code = mysqli_errno($this->link);

				if ($code == 1062) {
					$e = DuplicateObjectException::class;
				} else {
					$e = DatabaseException::class;
				}

				throw new $e(
					mysqli_error($this->link).' - '.$queryString,
					$code
				);
			}
			
			return $result;
		}
		
		public function getTableInfo($table)
		{
			static $types = array(
				'tinyint'		=> DataType::SMALLINT,
				'smallint'		=> DataType::SMALLINT,
				'int'			=> DataType::INTEGER,
				'mediumint'		=> DataType::INTEGER,

				'bigint'		=> DataType::BIGINT,
				
				'float'			=> DataType::REAL,
				'double'		=> DataType::DOUBLE,
				'decimal'		=> DataType::NUMERIC,

				'char'			=> DataType::CHAR,
				'varchar'		=> DataType::VARCHAR,
				'text'			=> DataType::TEXT,
				'tinytext'		=> DataType::TEXT,
				'mediumtext'	=> DataType::TEXT,
				'longtext'		=> DataType::TEXT,
				
				'date'			=> DataType::DATE,
				'time'			=> DataType::TIME,
				'timestamp'		=> DataType::TIMESTAMP,
				'datetime'		=> DataType::TIMESTAMP,

				// unhandled types
				'set'			=> null,
				'enum'			=> null,
				'year'			=> null
			);
			
			try {
				$result = $this->queryRaw('SHOW COLUMNS FROM '.$table);
			} catch (BaseException $e) {
				throw new ObjectNotFoundException(
					"unknown table '{$table}'"
				);
			}
			
			$table = new DBTable($table);
			
			while ($row = mysqli_fetch_array($result)) {
				$name = strtolower($row['Field']);
				$matches = array();
				$info = array('type' => null, 'extra' => null);
				if (
					preg_match(
						'~(\w+)(\((\d+?)\)){0,1}\s*(\w*)~',
						strtolower($row['Type']),
						$matches
					)
				) {
					$info['type'] = $matches[1];
					$info['size'] = $matches[3];
					$info['extra'] = $matches[4];
				}
				
				Assert::isTrue(
					array_key_exists($info['type'], $types),
					
					'unknown type "'
					.$types[$info['type']]
					.'" found in column "'.$name.'"'
				);
				
				if (empty($types[$info['type']]))
					continue;
				
				$column = DBColumn::create(
					DataType::create($types[$info['type']])->
						setUnsigned(
							strtolower($info['extra']) == 'unsigned'
						)->
						setNull(strtolower($row['Null']) == 'yes'),
					
					$name
				)->
				setAutoincrement(strtolower($row['Extra']) == 'auto_increment')->
				setPrimaryKey(strtolower($row['Key']) == 'pri');
				
				if ($row['Default'])
					$column->setDefault($row['Default']);
				
				$table->addColumn($column);
			}
			
			return $table;
		}
		
		public function hasQueue()
		{
			return false;
		}
		
		protected function getInsertId()
		{
			return mysqli_insert_id($this->link);
		}
		
		/**
		 * @return MyImprovedDialect
		**/
		protected function spawnDialect()
		{
			return new MyImprovedDialect();
		}
		
		private function checkSingle($result)
		{
			if (mysqli_num_rows($result) > 1)
				throw new TooManyRowsException(
					'query returned too many rows (we need only one)'
				);
			
			return $result;
		}

		protected  function setupAutoCommit()
		{
			if ($this->isConnected()) {
				mysqli_autocommit($this->link, $this->needAutoCommit);
			}
		}

		protected  function setupDefaultEngine()
		{
			if ($this->defaultEngine && $this->isConnected()) {
				mysqli_query($this->link, 'SET storage_engine='.$this->defaultEngine);
			}
		}
	}
?>