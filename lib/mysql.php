<?php

/**
 * Проверяем наш глобальный флаг состояния на наличие соединения.
 * 
 * @global PDO $STATEMENT
 * @return boolean
 */
function isConnected()
{
  GLOBAL $STATEMENT;
  
  return isset($STATEMENT['mysql']);
}

/**
 * Коннектимся к базе используя ПДО и используя
 * настройки.
 * 
 * @return \PDO|boolean
 */
function mysqlConnect()
{
  if (isConnected()) return true;
  
  $dsn = mysqlCreateConfig();
  
  try {
    $db = new PDO($dsn, mysqlGetConfig('user'), mysqlGetConfig('pass'));
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->exec("set names utf8");
  }
  catch(PDOException $e) {
      echo $e->getMessage();
  }
  
  return $db;
}

/**
 * Выполнение проивльного запроса к БД.
 * 
 * @param string $query
 * @return boolean
 */
function mysqlExec($query)
{
  $result = false;
  try
  {
    $result = getConnection()->exec($query);
  }
  catch(PDOException $e)
  {
    echo 'Error : '.$e->getMessage();
    exit();
  }
  
  return $result;
}

/**
 * Выполнение запроса к БД.
 * 
 * @param string $query
 * @return PDOStatement
 */
function mysqlQuery($query)
{
  $result = false;
  try
  {
    $connection = getConnection();
    $result     = $connection->query($query);
  }
  catch(PDOException $e)
  {
    echo 'Error : '.$e->getMessage();
    exit();
  }
  
  return $result;
}

/**
 * Выполение запроса с подготовкой данных.
 * 
 * @param string $query
 * @param array $params
 * @return boolean
 */
function mysqlPrepare($query, $params)
{
  $result = false;
  
  try
  {
    $connection = getConnection();
    $stmt       = $connection->prepare($query);
    $result     = $stmt->execute($params);
  }
  catch(PDOException $e)
  {
    echo 'Error : '.$e->getMessage();
    exit();
  }
  
  return $result;
}

/**
 * Выполнение запроса с подготовкой для чтения.
 * 
 * @param string $query
 * @param array $params
 * @return array
 */
function mysqlFetchAll($query, $params)
{
  $result = false;
  
  try
  {
    $connection = getConnection();
    $stmt       = $connection->prepare($query);
    
    if ($stmt->execute($params))
    {
      $result = $stmt->fetchAll(); 
    }
  }
  catch(PDOException $e)
  {
    echo 'Error : '.$e->getMessage();
    exit();
  }
  
  return $result;
}

/**
 * Выборка только первого значения.
 * 
 * @param string $table
 * @param array $what
 * @param array $where
 * @param string $whereCondition
 * @return array
 */
function mysqlSelectOne($table, $what, $where = array(), $whereCondition = 'AND')
{
  $selected = mysqlSelect($table, $what, $where, $whereCondition);
  
  return array_shift($selected);
}

/**
 * Выборка.
 * 
 * @param string $table
 * @param array $what
 * @param array $where
 * @param string $whereCondition
 * @return array
 */
function mysqlSelect($table, $what, $where = array(), $whereCondition = 'AND')
{
  $query = sprintf('SELECT %s FROM %s WHERE %s',
    mysqlPrepareFields($what),
    $table,
    mysqlPrepareWhere(array_keys($where), $whereCondition)
  );
  
  return mysqlFetchAll($query, $where);
}

/**
 * Обновление данных.
 * 
 * @param string $table
 * @param array $what
 * @param array $where
 * @param string $condition
 * @return boolean
 */
function mysqlUpdate($table, $what, $where, $condition = 'AND')
{
  $query = sprintf('UPDATE %s SET %s WHERE %s',
    $table,
    mysqlPrepareUpdateSet($what),
    mysqlPrepareWhere(array_keys($where), $condition, 'w_')
  );
  
  return mysqlPrepare($query, mysqlUpdateSetMerge($what, $where));
}

/**
 * Вставка данных.
 * 
 * @param string $table
 * @param array $what
 * @param array $where
 * @param string $condition
 * @return boolean
 */
function mysqlInsert($table, $what)
{
  $insert = arrayAddPrefixKeys($what, 'i_');
  
  $query = sprintf('INSERT INTO %s (%s) VALUES(%s)',
    $table,
    implode(', ', array_keys($what)),
    implode(', ', array_keys(arrayAddPrefixKeys($what, ':i_')))
  );
  
  return mysqlPrepare($query, $insert);
}

/**
 * Удаление данных.
 * 
 * @param string $table
 * @param array $where
 * @return boolean
 */
function mysqlDelete($table, $where)
{
  $query = sprintf('DELETE FROM %s WHERE %s',
    $table,
    mysqlPrepareWhere(array_keys($where), 'AND', 'w_')
  );
  
  return mysqlPrepare($query, arrayAddPrefixKeys($where, 'w_'));
}

/**
 * Подготовка массива полей для вклинивания в запрос.
 * 
 * @param array $fields
 * @return string
 */
function mysqlPrepareFields($fields)
{
  if (!is_array($fields)) return $fields;
  
  return implode(', ', $fields);
}

/**
 * Подготовка where условия из массива.
 * Префикс нужен для того что бы потом в препаре
 * не было дубляжа ключе если изменение или еще что
 * будут по одним и тем же полям.
 * 
 * @param array $where
 * @param string $condition
 * @param string $paramPrefix
 * @return string
 */
function mysqlPrepareWhere($where, $condition, $paramPrefix = '')
{
  if (!$where) return 1;
  if (!is_array($where)) return $where;
  
  $compiled = array();
  foreach ($where as $field)
  {
    $compiled[$field] = sprintf('%s = :%s%s', $field, $paramPrefix, $field);
  }
  
  return implode(sprintf(' %s ', $condition), $compiled);
}

/**
 * Подготовка апдейта.
 * 
 * @param array $update
 * @return string
 */
function mysqlPrepareUpdateSet($update)
{
  return mysqlPrepareWhere(array_keys($update), ',', 's_');
}

/**
 * Получение мускл конфига для PDO коннекта.
 * 
 * @return array
 */
function mysqlCreateConfig()
{
  return sprintf('%s:host=%s;dbname=%s',
    mysqlGetConfig('type'),
    mysqlGetConfig('host'),
    mysqlGetConfig('db')
  );
}

/**
 * Соединение полей для апдейта.
 * Опять же префиксы нужны для исключения пересечения
 * названия полей в разных операциях.
 * 
 * @param array $set
 * @param array $where
 * @return array
 */
function mysqlUpdateSetMerge($set, $where)
{
  return array_merge(
    arrayAddPrefixKeys($set, 's_'),
    arrayAddPrefixKeys($where, 'w_')
  );
}

/**
 * Получить конфиг мускла из его области.
 * 
 * @param string $name
 * @return mixed
 */
function mysqlGetConfig($name)
{
  $value = false;
  
  if ($config = loadConfig('mysql'))
  {
    $value = isset($config[$name]) ? $config[$name] : false;
  }
  
  return $value;
}

/**
 * Получить состояние PDO из нашей глобальной области.
 * 
 * @global array $STATEMENT
 * @param PDO $connection
 * @return PDO
 */
function mysqlSaveConnection($connection)
{
  global $STATEMENT;
  
  return $STATEMENT['mysql'] = $connection;
}

/**
 * Получить наше соединение из сохраненного состояния.
 * 
 * @return PDO
 */
function getConnection()
{
  $connection = false;
  
  if (!isConnected())
  {
    $connection = mysqlSaveConnection(mysqlConnect());
  }
  
  return $connection;
}