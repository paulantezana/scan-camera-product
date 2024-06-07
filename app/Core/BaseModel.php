<?php

namespace App\Core;

use App\Exceptions\DatabaseExeption;
use Exception;
use PDO;

class BaseModel
{
  protected string $table;
  protected string $id;
  protected PDO $db;

  public function __construct(string $table, string $id, PDO $connection)
  {
    $this->table = $table;
    $this->id = $id;
    $this->db = $connection;
  }

  // All
  public function getAll(string $prefix = '')
  {
    $prefix = $this->assemblyPrefix($prefix);
    $stmt = $this->db->prepare("SELECT * FROM {$prefix}{$this->table}");
    if (!$stmt->execute()) {
      throw new Exception($stmt->errorInfo()[2]);
    }
    return $stmt->fetchAll();
  }

  // Paginate
  public function paginate(array $pageRequest, string $prefix = '')
  {
    $prefix = $this->assemblyPrefix($prefix);

    $whreSql = $this->buildWhereSql($pageRequest['filter'] ?? [], $pageRequest['aditionalFilter'] ?? '', $pageRequest['alias'] ?? '');
    $sortSql = $this->buildSortSql($pageRequest['sorter'] ?? [], $pageRequest['alias'] ?? '');

    $offset = ($pageRequest['page'] - 1) * $pageRequest['limit'];
    $totalRows = $this->db->query("SELECT COUNT(1) FROM {$prefix}{$this->table} {$whreSql}")->fetchColumn();
    $totalPages = ceil($totalRows / $pageRequest['limit']);

    $stmt = $this->db->prepare("SELECT * FROM {$prefix}{$this->table} {$whreSql} {$sortSql} LIMIT {$offset}, {$pageRequest['limit']}");

    if (!$stmt->execute()) {
      throw new Exception($stmt->errorInfo()[2]);
    }
    $data = $stmt->fetchAll();

    return [
      'current' => $pageRequest['page'],
      'pages' => $totalPages,
      'limit' => $pageRequest['limit'],
      'data' => $data,
      'total' => $totalRows,
    ];
  }

  // Get by id
  public function getById(int $id, string $prefix = '')
  {
    $prefix = $this->assemblyPrefix($prefix);

    $stmt = $this->db->prepare("SELECT * FROM {$prefix}{$this->table} WHERE $this->id = :$this->id LIMIT 1");
    $stmt->bindValue(":$this->id", $id);
    if (!$stmt->execute()) {
      throw new Exception($stmt->errorInfo()[2]);
    }
    return $stmt->fetch();
  }

  // Get by filter
  public function getBy(string $columnName, $value, string $prefix = '')
  {
    $prefix = $this->assemblyPrefix($prefix);

    $stmt = $this->db->prepare("SELECT * FROM {$prefix}{$this->table} WHERE {$columnName} = :column_value LIMIT 1");
    $stmt->bindValue(":column_value", $value);
    if (!$stmt->execute()) {
      throw new Exception($stmt->errorInfo()[2]);
    }
    return $stmt->fetch();
  }

  // Delete
  public function deleteById(int $id)
  {
    try {
      $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE {$this->id} = :{$this->id}");
      $stmt->bindValue(":{$this->id}", $id);
      if (!$stmt->execute()) {
        throw new Exception($stmt->errorInfo()[2]);
      }

      return $id;
    } catch (\PDOException $ex) {
      if ($ex->getCode() == 23000) {
        throw new DatabaseExeption("Este registro no se puede eliminar debido a que uno o más tablas dependen de este registro. VALIDACIÓN: " . $stmt->errorInfo()[2], $ex->getCode(), $ex);
      } else {
        throw $ex;
      }
    }
  }

  public function deleteBy(string $columnName, $value)
  {
    try {
      $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE {$columnName} = :column_value");
      $stmt->bindValue(":column_value", $value);
      if (!$stmt->execute()) {
        throw new Exception($stmt->errorInfo()[2]);
      }
      return $value;
    } catch (\PDOException $ex) {
      if ($ex->getCode() == 23000) {
        throw new DatabaseExeption("Este registro no se puede eliminar debido a que uno o más tablas dependen de este registro. VALIDACIÓN: " . $stmt->errorInfo()[2], $ex->getCode(), $ex);
      } else {
        throw $ex;
      }
    }
  }

  // Update
  public function updateById(int $id, array $data, $audit = true)
  {
    // Audit
    $data = $this->setAudit($data, $audit, true);

    // Update
    $columnUpdates = [];
    foreach ($data as $key => $value) {
      $columnUpdates[] = "{$key} = :{$key}";
    }
    $columnUpdatesString = implode(", ", $columnUpdates);

    $sql = "UPDATE {$this->table} SET {$columnUpdatesString} WHERE {$this->id} = :{$this->id}";

    try {
      $stmt = $this->db->prepare($sql);

      foreach ($data as $key => $rowValue) {
        $paramType = \PDO::PARAM_STR;
        if (is_bool($rowValue)) {
          $paramType = \PDO::PARAM_BOOL;
        } elseif (is_int($rowValue)) {
          $paramType = \PDO::PARAM_INT;
        } elseif ($rowValue === "null" || $rowValue === null) {
          $paramType = \PDO::PARAM_NULL;
        }
        $stmt->bindValue(":{$key}", $rowValue, $paramType);
      }
      $stmt->bindValue(":{$this->id}", $id);

      if (!$stmt->execute()) {
        throw new Exception($stmt->errorInfo()[2]);
      }

      return $id;
    } catch (\PDOException $e) {
      if ($e->getCode() === '23000') {
        throw new DatabaseExeption($stmt->errorInfo()[2], $e->getCode(), $e);
      } else {
        throw new Exception("Error al actualizar datos: " . $e->getMessage());
      }
    }
  }

  public function updateBy(string $columnName, $value, array $data, $audit = true)
  {
    // Audit
    $data = $this->setAudit($data, $audit, true);

    // Update
    $columnUpdates = [];
    foreach ($data as $key => $rowValue) {
      $columnUpdates[] = "{$key} = :{$key}";
    }
    $columnUpdatesString = implode(", ", $columnUpdates);

    $sql = "UPDATE {$this->table} SET {$columnUpdatesString} WHERE {$columnName} = :value";

    try {
      $stmt = $this->db->prepare($sql);

      foreach ($data as $key => $rowValue) {
        $paramType = \PDO::PARAM_STR;
        if (is_bool($rowValue)) {
          $paramType = \PDO::PARAM_BOOL;
        } elseif (is_int($rowValue)) {
          $paramType = \PDO::PARAM_INT;
        } elseif ($rowValue === "null" || $rowValue === null) {
          $paramType = \PDO::PARAM_NULL;
        }
        $stmt->bindValue(":{$key}", $rowValue, $paramType);
      }
      $stmt->bindValue(":value", $value);

      if (!$stmt->execute()) {
        throw new Exception($stmt->errorInfo()[2]);
      }

      return true;
    } catch (\PDOException $e) {
      if ($e->getCode() === '23000') {
        throw new DatabaseExeption($stmt->errorInfo()[2], $e->getCode(), $e);
      } else {
        throw $e;
      }
    }
  }

  // Insert
  public function insert(array $data, $audit = true)
  {
    $data = $this->setAudit($data, $audit, false);

    // Insert Params
    $columns = array_keys($data);
    $columnNames = implode(", ", $columns);
    $columnPlaceholders = implode(", :", $columns);

    // SQL Statement
    $sql = "INSERT INTO {$this->table} ({$columnNames}) VALUES (:{$columnPlaceholders})";

    try {
      $stmt = $this->db->prepare($sql);

      foreach ($data as $key => $rowValue) {
        $paramType = \PDO::PARAM_STR;
        if (is_bool($rowValue)) {
          $paramType = \PDO::PARAM_BOOL;
        } elseif (is_int($rowValue)) {
          $paramType = \PDO::PARAM_INT;
        } elseif ($rowValue === "null" || $rowValue === null) {
          $paramType = \PDO::PARAM_NULL;
        }
        $stmt->bindValue(":{$key}", $rowValue, $paramType);
      }

      if (!$stmt->execute()) {
        throw new Exception($stmt->errorInfo()[2]);
      }

      return $this->db->lastInsertId();
    } catch (\PDOException $e) {
      if ($e->getCode() === '23000') {
        throw new DatabaseExeption($stmt->errorInfo()[2], $e->getCode(), $e);
      } else {
        throw $e;
      }
    }
  }

  // Set Audit
  public function setAudit(array $data, $audit, bool $update = false)
  {
    // Default values
    $currentDate = date('Y-m-d H:i:s');

    if (is_bool($audit)) {
      if ($audit) {
        if ($update) {
          $auditData = [
            'updated_at' => $currentDate,
            'updated_user' => $_SESSION[SESS_USER]['user_name'] ?? '',
          ];
        } else {
          $auditData = [
            'created_at' => $currentDate,
            'created_user' => $_SESSION[SESS_USER]['user_name'] ?? '',
          ];
        }
        $data = array_merge($data, $auditData);
      }
    } elseif (is_array($audit)) {
      if ($update) {
        $auditData = [
          'updated_at' => $currentDate,
          'updated_user' => $audit['userName'] ?? '',
        ];
      } else {
        $auditData = [
          'created_at' => $currentDate,
          'created_user' => $audit['userName'] ?? '',
        ];
      }
      $data = array_merge($data, $auditData);
    }

    return $data;
  }



  private function translateSqlOperator(string $operator, $value1, $value2)
  {
    $operationSql = '';
    $value1Slash = addcslashes($value1, "'");
    $valueSlash = addcslashes($value2, "'");

    switch (strtoupper($operator)) {
      case "CONTIENE":
        $operationSql = "LIKE '%{$value1Slash}%'";
        break;
      case "EMPIEZA POR":
        $operationSql = "LIKE '{$value1Slash}%'";
        break;
      case "ES":
      case "=":
        $operationSql = "= '{$value1Slash}'";
        break;
      case "<>":
      case "NO ES":
        $operationSql = "!= '{$value1Slash}'";
        break;
      case "NO CONTIENE":
        $operationSql = "NOT LIKE '%{$value1Slash}%'";
        break;
      case "SE ENCUENTRA ENTRE (INCLUYE)":
        $operationSql = "BETWEEN '{$value1Slash}' AND '{$valueSlash}'";
        break;
      case "<":
        $operationSql = "< '{$value1Slash}'";
        break;
      case ">":
        $operationSql = "> '{$value1Slash}'";
        break;
      case "ES MENOR QUE E INCLUYE":
      case "<=":
        $operationSql = "<= '{$value1Slash}'";
        break;
      case "ES MAYOR QUE E INCLUYE":
      case ">=":
        $operationSql = ">= '{$value1Slash}'";
        break;
    }

    return $operationSql;
  }

  private function translateFilterToSqlCondition(array $filters, string $alias = '')
  {
    $whreSql = '';
    if ($alias != '') {
      $alias = $alias . '.';
    }

    $ki = 0;

    foreach ($filters as $k => $queryRow) {
      if (!isset($queryRow['eval'])) {
        continue;
      }
      if (count($queryRow['eval']) === 0) {
        continue;
      }

      $whreSql .= $ki === 0 ? '(' : " {$queryRow['logicalOperator']} (";
      foreach ($queryRow['eval'] as $key => $row) {
        $whreSql .= $key === 0 ? '' :  " {$row['logicalOperator']} ";
        if (strtoupper($row['prefix']) === 'DONDE NO') {
          $whreSql .= " NOT ({$this->formatField($alias,$row)} {$this->translateSqlOperator($row['operator'],$row['value1'] ?? '',$row['value2'] ?? '')})";
        } else {
          $whreSql .= "({$this->formatField($alias,$row)} {$this->translateSqlOperator($row['operator'],$row['value1'] ?? '',$row['value2'] ?? '')})";
        }
      }
      $whreSql .= ")";
      $ki++;
    }
    return trim($whreSql);
  }

  protected function formatField($alias, $row)
  {
    if (($row['type'] ?? '') == 'date') {
      return "DATE_FORMAT({$alias}{$row['field']}, '%Y-%m-%d')";
    }
    return "{$alias}{$row['field']}";
  }

  protected function buildWhereSql(array $filters, string $additionalFilter = '', string $alias = '')
  {
    $sqlCondition = $this->translateFilterToSqlCondition($filters, $alias);

    if ((strlen(trim($additionalFilter)) > 0 && strlen(trim($sqlCondition)) > 0)) {
      $sqlCondition = "{$additionalFilter} AND {$sqlCondition}";
    } else {
      $sqlCondition = trim("{$additionalFilter} {$sqlCondition}");
    }

    $sqlCondition = trim(strlen(trim($sqlCondition)) === 0 ? '' : "WHERE {$sqlCondition}");

    return $sqlCondition;
  }

  protected function buildSortSql(array $sort, string $alias = '')
  {
    $sortSql = '';
    if ($alias != '') {
      $alias = $alias . '.';
    }

    if (count($sort) > 0) {
      $sortSql = "ORDER BY {$alias}{$sort['field']} {$sort['order']}";
    }
    return $sortSql;
  }

  protected function assemblyPrefix(string $prefix)
  {
    if (strlen($prefix) > 0) {
      return $prefix . '_';
    } else {
      return '';
    }
  }
}
