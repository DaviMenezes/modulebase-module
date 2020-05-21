<?php

namespace Modules\ModuleBase\Repository;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

class RepositoryBase extends RepositoryBaseAbstract
{
    /**@var Model*/
    protected $model;

    public function model()
    {
        $class = $this->domain->modelClass();
        return $this->model = $this->model ?? new $class();
    }

    /**@return Response*/
    public function all()
    {
        return $this->exec(function () {
            $result = Cache::remember($this->getCacheKey(), $this->domain->cache_seconds, function() {
                return $this->domain->modelClass()::all();
            });
            $data['items'] = $result->toArray();
            $data['total'] = $result->count();
            return $data;
        });
    }

    public function fill(array $data)
    {
        $this->model()->fill($data);
        return  $this;
    }

    public function create(array $data)
    {
        return $this->domain->modelClass()::query()->create($data);
    }

    public function update($data)
    {
        return $this->domain->modelClass()::query()->findOrFail($data['id'])->fill($data)->save();
    }

    public function destroy(array $ids)
    {
        return $this->exec(function () use($ids) {
            return $this->domain->modelClass()::destroy($ids);
        });
    }

    public function save()
    {
        return $this->model()->save();
    }

    /**@return Response*/
    public function getById($id)
    {
        return $this->exec(function () use ($id) {
            $model = $this->domain->modelClass();
            return $model::query()->findOrFail($id);
        });
    }

    public function response()
    {
        return response($this->model(), 200, ['application/json']);
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

    public function paginate($per_page)
    {
        $cache_key = $this->getCacheKey();

        return Cache::remember($cache_key, $this->domain->cache_seconds, function () use ($per_page){
            return $this->model()::query()->paginate($per_page);
        });
    }

    protected function getCacheKey(): string
    {
        $uri_collection = collect(explode('/', request()->getRequestUri()))->filter();
        if ($uri_collection->first() == 'api') {
            $uri_collection->shift();
        }
        $cache_key = $uri_collection->join('/');
        return $cache_key;
    }
}
