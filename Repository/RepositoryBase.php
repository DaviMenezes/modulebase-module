<?php

namespace Modules\ModuleBase\Repository;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Response;
use Modules\ModuleBase\Repository\RepositoryBaseAbstract;

class RepositoryBase extends RepositoryBaseAbstract
{
    /**@var Model*/
    protected $model;

    protected function model()
    {
        $class = $this->domain->modelClass();
        return $this->model = $this->model ?? new $class();
    }

    /**@return Response*/
    public function all()
    {
        return $this->exec(function () {
            return $this->domain->modelClass()::all();
        });
    }

    /**@return Response*/
    public function fill(array $data)
    {
        return $this->exec(function () use ($data) {
            return $this->model()->fill($data);
        });
    }

    /**@return Response*/
    public function create(array $data)
    {
        return $this->exec(function () use ($data) {
            return $this->domain->modelClass()::query()->create($data);
        });
    }

    public function update($id, $data)
    {
        return $this->exec(function () use($id, $data) {
            return $this->domain->modelClass()::query()->findOrFail($id)->fill($data)->save();
        });
    }

    /**@return Response*/
    public function save()
    {
        return $this->exec(function () {
            return $this->model()->save();
        });
    }

    /**@return Response*/
    public function getById($id)
    {
        return $this->exec(function () use ($id) {
            $model = $this->domain->modelClass();
            return $model::query()->findOrFail($id);
        });
    }

    /**@return Response*/
    protected function exec(callable $function)
    {
        try {
            return response($function(), 200, ['application/json']);
        } catch (\Exception $exception) {
            $msg['status'] = 'error';
            $msg['msg'] = $exception->getMessage();
            $msg['files'] = $exception->getFile(). ' in line '. $exception->getLine()."\n";
            $msg['code'] = $exception->getCode()."\n";
            $msg['trace'] = $exception->getPrevious();
            if (env('APP_ENV') !== 'production' && request()->method() == 'GET') {
//                Debugbar::addThrowable($exception);
            }
            return response($msg, 400, ['application/json']);
        }
    }
}
