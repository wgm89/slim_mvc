<?php
/*!
 * Medoo database framework
 * http://medoo.in
 * Version 0.8.2
 * 
 * Copyright 2013, Angel Lai
 * Released under the MIT license
 */

class Medoo
{
	protected $database_type = 'mysql';

	// For MySQL, MSSQL, Sybase
	protected $server = 'localhost';
	
	protected $username = 'username';
	
	protected $password = 'password';

	// For SQLite
	protected $database_file = '';

	// Optional
	protected $charset = 'utf8';
	protected $database_name = '';
	protected $option = array();
    
    //diffrent place
    protected $dbconfig = '';
    protected $table = ''; 

    public function __construct()
    {
        $param = func_get_args();
        if(!empty($param)){
            $options = $param[0];
            try {
                $type = strtolower($this->database_type);

                if (is_string($options))
                {
                    if ($type == 'sqlite')
                    {
                        $this->database_file = $options;
                    }
                    else
                    {
                        $this->database_name = $options;
                    }
                }
                else
                {
                    foreach ($options as $option => $value)
                    {
                        $this->$option = $value;
                    }
                }

                $type = strtolower($this->database_type);
                $this->setpdo($type);
            }
            catch (PDOException $e) {
                echo $e->getMessage();
            }
        }else{
      
        }
    }
    public function loadconfig($dbconfig){
        $db = load_db_cfg($dbconfig);
        $this->database_name = $db['database'];
        $this->username = $db['username'];
        $this->password = $db['password'];
        $this->setpdo($db['type']);
        return $this;
    }
    public function table($table){
        $this->table = $table;
        return $this;
    }
    protected function setpdo($type){
        switch ($type)
        {
        case 'mysql':
        case 'pgsql':
            $this->pdo = new PDO(
                $type . ':host=' . $this->server . ';dbname=' . $this->database_name, 
                $this->username,
                $this->password,
                $this->option
            );
            break;

        case 'mssql':
        case 'sybase':
            $this->pdo = new PDO(
                $type . ':host=' . $this->server . ';dbname=' . $this->database_name . ',' .
                $this->username . ',' .
                $this->password,
                $this->option
            );
            break;

        case 'sqlite':
            $this->pdo = new PDO(
                $type . ':' . $this->database_file,
                $this->option
            );
            break;
        }
        $this->pdo->exec('SET NAMES \'' . $this->charset . '\'');
    }
	
	public function query($query)
	{
		$this->queryString = $query;
		
		return $this->pdo->query($query);
	}

	public function exec($query)
	{
		$this->queryString = $query;

		return $this->pdo->exec($query);
	}

	public function quote($string)
	{
		return $this->pdo->quote($string);
	}

	protected function array_quote($array)
	{
		$temp = array();
		foreach ($array as $value)
		{
			$temp[] = is_int($value) ? $value : $this->pdo->quote($value);
		}

		return implode($temp, ',');
	}
	
	protected function inner_conjunct($data, $conjunctor, $outer_conjunctor)
	{
		$haystack = array();
		foreach ($data as $value)
		{
			$haystack[] = '(' . $this->data_implode($value, $conjunctor) . ')';
		}

		return implode($outer_conjunctor . ' ', $haystack);
	}

	protected function data_implode($data, $conjunctor, $outer_conjunctor = null)
	{
		$wheres = array();
		foreach ($data as $key => $value)
		{
			if (($key == 'AND' || $key == 'OR') && is_array($value))
			{
				$wheres[] = 0 !== count(array_diff_key($value, array_keys(array_keys($value)))) ?
					'(' . $this->data_implode($value, ' ' . $key) . ')' :
					'(' . $this->inner_conjunct($value, ' ' . $key, $conjunctor) . ')';
			}
			else
			{
				preg_match('/([\w\.]+)(\[(\>|\>\=|\<|\<\=|\!|\<\>)\])?/i', $key, $match);
				if (isset($match[3]))
				{
					if ($match[3] == '' || $match[3] == '!')
					{
						$wheres[] = $match[1] . ' ' . $match[3] . '= ' . $this->quote($value);
					}
					else
					{
						if ($match[3] == '<>')
						{
							if (is_array($value))
							{
								if (is_numeric($value[0]) && is_numeric($value[1]))
								{
									$wheres[] = $match[1] . ' BETWEEN ' . $value[0] . ' AND ' . $value[1];
								}
								else
								{
									$wheres[] = $match[1] . ' BETWEEN ' . $this->quote($value[0]) . ' AND ' . $this->quote($value[1]);
								}
							}
						}
						else
						{
							if (is_numeric($value))
							{
								$wheres[] = $match[1] . ' ' . $match[3] . ' ' . $value;
							}
						}
					}
				}
				else
				{
					if (is_int($key))
					{
						$wheres[] = $this->quote($value);
					}
					else
					{
						$wheres[] = is_array($value) ? $match[1] . ' IN (' . $this->array_quote($value) . ')' :
							$match[1] . ' = ' . $this->quote($value);
					}
				}
			}
		}

		return implode($conjunctor . ' ', $wheres);
	}

	public function where_clause($where)
	{
		$where_clause = '';
		if (is_array($where))
		{
			$single_condition = array_diff_key($where, array_flip(
				array('AND', 'OR', 'GROUP', 'ORDER', 'HAVING', 'LIMIT', 'LIKE', 'MATCH')
			));
			if ($single_condition != array())
			{
				$where_clause = ' WHERE ' . $this->data_implode($single_condition, '');
			}
			if (isset($where['AND']))
			{
				$where_clause = ' WHERE ' . $this->data_implode($where['AND'], ' AND ');
			}
			if (isset($where['OR']))
			{
				$where_clause = ' WHERE ' . $this->data_implode($where['OR'], ' OR ');
			}
			if (isset($where['LIKE']))
			{
				$like_query = $where['LIKE'];
				if (is_array($like_query))
				{
					if (isset($like_query['OR']) || isset($like_query['AND']))
					{
						$connector = isset($like_query['OR']) ? 'OR' : 'AND';
						$like_query = isset($like_query['OR']) ? $like_query['OR'] : $like_query['AND'];
					}
					else
					{
						$connector = 'AND';
					}

					$clause_wrap = array();
					foreach ($like_query as $column => $keyword)
					{
						if (is_array($keyword))
						{
							foreach ($keyword as $key)
							{
								$clause_wrap[] = $column . ' LIKE ' . $this->quote('%' . $key . '%');
							}
						}
						else
						{
							$clause_wrap[] = $column . ' LIKE ' . $this->quote('%' . $keyword . '%');
						}
					}
					$where_clause .= ($where_clause != '' ? ' AND ' : ' WHERE ') . '(' . implode($clause_wrap, ' ' . $connector . ' ') . ')';
				}
			}
			if (isset($where['MATCH']))
			{
				$match_query = $where['MATCH'];
				if (is_array($match_query) && isset($match_query['columns']) && isset($match_query['keyword']))
				{
					$where_clause .= ($where_clause != '' ? ' AND ' : ' WHERE ') . ' MATCH (' . implode($match_query['columns'], ', ') . ') AGAINST (' . $this->quote($match_query['keyword']) . ')';
				}
			}
			if (isset($where['GROUP']))
			{
				$where_clause .= ' GROUP BY ' . $where['GROUP'];
			}
			if (isset($where['ORDER']))
			{
				$where_clause .= ' ORDER BY ' . $where['ORDER'];
				if (isset($where['HAVING']))
				{
					$where_clause .= ' HAVING ' . $this->data_implode($where['HAVING'], '');
				}
			}
			if (isset($where['LIMIT']))
			{
				if (is_numeric($where['LIMIT']))
				{
					$where_clause .= ' LIMIT ' . $where['LIMIT'];
				}
				if (is_array($where['LIMIT']) && is_numeric($where['LIMIT'][0]) && is_numeric($where['LIMIT'][1]))
				{
					$where_clause .= ' LIMIT ' . $where['LIMIT'][0] . ',' . $where['LIMIT'][1];
				}
			}
		}
		else
		{
			if ($where != null)
			{
				$where_clause .= ' ' . $where;
			}
		}

		return $where_clause;
	}
		
	public function select($columns, $where = null, $ext = null)
	{
		$query = $this->query('SELECT ' . (
			is_array($columns) ? implode(', ', $columns) : $columns
		) . ' FROM ' . $this->table . $this->where_clause($where) .' ' .$ext);

		return $query ? $query->fetchAll(
			(is_string($columns) && $columns != '*') ? PDO::FETCH_COLUMN : PDO::FETCH_ASSOC
		) : false;
	}
		
	public function insert($data)
	{
		$keys = implode(',', array_keys($data));
		$values = array();
		foreach ($data as $key => $value)
		{
			$values[] = is_array($value) ? serialize($value) : $value;
		}
		$status = $this->query('INSERT INTO ' . $this->table . ' (' . $keys . ') VALUES (' . $this->data_implode(array_values($values), ',') . ')');
		$id = $this->pdo->lastInsertId();
		return isset($id)?$id:$status;
	}
	
	public function update($data, $where = null)
	{
		$fields = array();
		foreach ($data as $key => $value)
		{
			if (is_array($value))
			{
				$fields[] = $key . '=' . $this->quote(serialize($value));
			}
			else
			{
				preg_match('/([\w]+)(\[(\+|\-)\])?/i', $key, $match);
				if (isset($match[3]))
				{
					if (is_numeric($value))
					{
						$fields[] = $match[1] . ' = ' . $match[1] . ' ' . $match[3] . ' ' . $value;
					}
				}
				else
				{
					$fields[] = $key . ' = ' . $this->quote($value);
				}
			}
		}
		
		return $this->exec('UPDATE ' . $this->table . ' SET ' . implode(',', $fields) . $this->where_clause($where));
	}
	
	public function delete($where)
	{
		return $this->exec('DELETE FROM ' . $this->table . $this->where_clause($where));
	}
	
	public function replace($columns, $search = null, $replace = null, $where = null)
	{
		if (is_array($columns))
		{
			$replace_query = array();
			foreach ($columns as $column => $replacements)
			{
				foreach ($replacements as $replace_search => $replace_replacement)
				{
					$replace_query[] = $column . ' = REPLACE(' . $column . ', ' . $this->quote($replace_search) . ', ' . $this->quote($replace_replacement) . ')';
				}
			}
			$replace_query = implode(', ', $replace_query);
			$where = $search;
		}
		else
		{
			if (is_array($search))
			{
				$replace_query = array();
				foreach ($search as $replace_search => $replace_replacement)
				{
					$replace_query[] = $columns . ' = REPLACE(' . $columns . ', ' . $this->quote($replace_search) . ', ' . $this->quote($replace_replacement) . ')';
				}
				$replace_query = implode(', ', $replace_query);
				$where = $replace;
			}
			else
			{
				$replace_query = $columns . ' = REPLACE(' . $columns . ', ' . $this->quote($search) . ', ' . $this->quote($replace) . ')';
			}
		}

		return $this->exec('UPDATE ' . $this->table . ' SET ' . $replace_query . $this->where_clause($where));
	}

	public function get($columns, $where = null)
	{
		if (is_array($where))
		{
			$where['LIMIT'] = 1;
		}
		$data = $this->select($columns, $where);

		return isset($data[0]) ? $data[0] : false;
	}

	public function has($where)
	{
		return $this->query('SELECT EXISTS(SELECT 1 FROM ' . $this->table . $this->where_clause($where) . ')')->fetchColumn() === '1';
	}

	public function count($where = null)
	{
		return 0 + ($this->query('SELECT COUNT(*) FROM ' . $this->table . $this->where_clause($where))->fetchColumn());
	}

	public function max($column, $where = null)
	{
		return 0 + ($this->query('SELECT MAX(' . $column . ') FROM ' . $this->table . $this->where_clause($where))->fetchColumn());
	}

	public function min($column, $where = null)
	{
		return 0 + ($this->query('SELECT MIN(' . $column . ') FROM ' . $this->table . $this->where_clause($where))->fetchColumn());
	}

	public function avg($column, $where = null)
	{
		return 0 + ($this->query('SELECT AVG(' . $column . ') FROM ' . $this->table . $this->where_clause($where))->fetchColumn());
	}

	public function sum($column, $where = null)
	{
		return 0 + ($this->query('SELECT SUM(' . $column . ') FROM ' . $this->table . $this->where_clause($where))->fetchColumn());
	}

	public function error()
	{
		return $this->pdo->errorInfo();
	}

	public function last_query()
	{
		return $this->queryString;
	}

	public function info()
	{
		return array(
			'server' => $this->pdo->getAttribute(PDO::ATTR_SERVER_INFO),
			'client' => $this->pdo->getAttribute(PDO::ATTR_CLIENT_VERSION),
			'driver' => $this->pdo->getAttribute(PDO::ATTR_DRIVER_NAME),
			'version' => $this->pdo->getAttribute(PDO::ATTR_SERVER_VERSION),
			'connection' => $this->pdo->getAttribute(PDO::ATTR_CONNECTION_STATUS)
		);
	}
}
?>
