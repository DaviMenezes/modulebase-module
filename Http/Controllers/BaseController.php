<?php

namespace Modules\ModuleBase\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\ModuleBase\Domain\DomainBase;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

abstract class BaseController extends Controller
{
    /**@var Model*/
    protected $model_class;
    /**@var Model*/
    protected $model;

    /**@var DomainBase*/
    protected $entity;

    public function __construct()
    {
        $this->model_class = $this->modelClass();
    }

    /**@return  Model*/
    public function model() {
        $class = $this->model_class;
        return $this->model = $this->model ?? new $class();
    }

    /**@return Model*/
    public abstract function modelClass();

    public abstract function entityClass();

    /**@return DomainBase*/
    public function entity()
    {
        $class = $this->entityClass();
        return $this->entity = $this->entity ?? new $class();
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return $this->all();
    }

    public function all()
    {
        return $this->entity()->repository()->all();
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        return $this->entity()->repository()->create($request->all());
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        return $this->entity()->repository()->getById($id);
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        return
            $this->entity()
                ->repository()
                ->update($id, $request->all());

    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($ids)
    {
        return $this->entity()->repository()->destroy([$ids]);
    }
}
