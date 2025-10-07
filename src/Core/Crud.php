<?php
namespace Lourdian\BasicStudent\Core;

interface Crud {
    public function create($data);
    public function read($where = []);
    public function update($data, $where);
    public function delete($where);
}
