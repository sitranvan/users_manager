<?php

function query($sql, $data = [], $statementStatus = false): bool|PDOStatement
{
    global $conn;
    try {
        $statement = $conn->prepare($sql);
        $query = $statement->execute($data);
        if ($statementStatus && $query) {
            return $statement;
        }
    } catch (Exception $e) {
        echo 'Error query: ' . $e->getMessage();
        return false;
    }
    return $query;
}

function insert($table, $dataInsert): bool
{
    $fields = array_keys($dataInsert);
    $fieldStr = implode(', ', $fields);
    $placeholders = array_map(function ($field) {
        return ':' . $field;
    }, $fields);

    $placeholderStr = implode(', ', $placeholders);
    $sql = 'INSERT INTO ' . $table . '(' . $fieldStr . ') VALUES(' . $placeholderStr . ')';

    return query($sql, $dataInsert);
}


function deleteRecord($table, $condition)
{
    $sql = null;
    if (!empty($condition)) {
        $sql = 'DELETE FROM ' . $table . ' WHERE ' . $condition;
    }
    return query($sql);
}

function update($table, $dataUpdate, $condition): bool
{
    $updateFields = array_map(function ($key) {
        return $key . '=:' . $key;
    }, array_keys($dataUpdate));

    $updateStr = implode(', ', $updateFields);
    $sql = 'UPDATE ' . $table . ' SET ' . $updateStr;

    if (!empty($condition)) {
        $sql .= ' WHERE ' . $condition;
    }

    return query($sql, $dataUpdate);
}

// Lấy dữ liệu từ câu lệnh SQL
function getDraw($sql): bool|array
{
    $statement = query($sql, [], true);
    if (is_object($statement)) {
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
    return false;
}

// lấy về mảng 1 chiều
function firstDraw($sql): bool|array
{
    $statement = query($sql, [], true);
    if (is_object($statement)) {
        return $statement->fetch(PDO::FETCH_ASSOC);
    }
    return false;
}

// lấy dữ liệu từ table cụ thể

function get($table, $fields = '*', $condition = ''): bool|array
{
    $sql = 'SELECT ' . $fields . ' FROM ' . $table;

    if (!empty($condition)) {
        $sql .= ' WHERE ' . $condition;
    }

    return getDraw($sql);
}
// lấy về mảng 1 chiều, dùng fetch cũng được nhưng chỉ trả về 1 dữ liệu duy nhất dưới dạng mảng 2 chiều -> khó xử lí
function first($table, $fields = '*', $condition = ''): bool|array
{
    $sql = 'SELECT ' . $fields . ' FROM ' . $table;

    if (!empty($condition)) {
        $sql .= ' WHERE ' . $condition;
    }

    return firstDraw($sql);
}

// lấy số dòng câu truy vấn
function getRows($sql)
{
    $statement = query($sql, [], true);
    if (!empty($statement)) {
        return $statement->rowCount();
    }
}
