<?php

namespace Modules\ModuleBase\Repository;

interface RepositoryInterface
{
    public function fill(array $data);
    public function save();
    public function getById($id);
}
