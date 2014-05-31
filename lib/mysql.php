<?php

function isConnected()
{
  GLOBAL $STATEMENT;
  
  return isset($STATEMENT['mysql']);
}

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
    echo $connection->errorCode();
    echo $connection->errorInfo();
    echo 'Error : '.$e->getMessage();
    exit();
  }
  
  return $result;
}

/**
 * 
 * @param type $query
 * @param type $params
 * @return array
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
 * 
 * @param type $query
 * @param type $params
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

function mysqlSelectOne($table, $what, $where = array(), $whereCondition = 'AND')
{
  $selected = mysqlSelect($table, $what, $where, $whereCondition);
  
  return array_shift($selected);
}

function mysqlSelect($table, $what, $where = array(), $whereCondition = 'AND')
{
  $query = sprintf('SELECT %s FROM %s WHERE %s',
    mysqlPrepareFields($what),
    $table,
    mysqlPrepareWhere(array_keys($where), $whereCondition)
  );
  
  return mysqlFetchAll($query, $where);
}

function mysqlUpdate($table, $what, $where, $condition = 'AND')
{
  $query = sprintf('UPDATE %s SET %s WHERE %s',
    $table,
    mysqlPrepareUpdateSet($what),
    mysqlPrepareWhere(array_keys($where), $condition, 'w_')
  );
  
  return mysqlPrepare($query, mysqlUpdateSetMerge($what, $where));
}

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

function mysqlDelete($table, $where)
{
  $query = sprintf('DELETE FROM %s WHERE %s',
    $table,
    mysqlPrepareWhere(array_keys($where), 'AND', 'w_')
  );
  
  return mysqlPrepare($query, arrayAddPrefixKeys($where, 'w_'));
}
        
function mysqlPrepareFields($fields)
{
  if (!is_array($fields)) return $fields;
  
  return implode(', ', $fields);
}

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

function mysqlPrepareUpdateSet($update)
{
  return mysqlPrepareWhere(array_keys($update), ',', 's_');
}

function mysqlCreateConfig()
{
  return sprintf('%s:host=%s;dbname=%s',
    mysqlGetConfig('type'),
    mysqlGetConfig('host'),
    mysqlGetConfig('db')
  );
}

function mysqlUpdateSetMerge($set, $where)
{
  return array_merge(
    arrayAddPrefixKeys($set, 's_'),
    arrayAddPrefixKeys($where, 'w_')
  );
}

/**
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