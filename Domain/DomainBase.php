<?php

namespace Modules\ModuleBase\Domain;

use Illuminate\Database\Eloquent\Model;
use Modules\ModuleBase\Entites\FactoryBase;
use Modules\ModuleBase\Repository\RepositoryBase;

abstract class DomainBase
{
    protected $factory;

    protected $repository;

    public $cache_seconds = 60*60*2;

    //ABSTRACT METHODS__________________________________________________________________________________________________
    /**@return string|Model*/
    public abstract function modelClass();
    //__________________________________________________________________________________________________________________

    protected function repositoryClass() {
        return RepositoryBase::class;
    }

    protected function getFactory()
    {
        return FactoryBase::class;
    }

    public function factory():FactoryBase
    {
        $factory = $this->getFactory();
        return $this->factory = $this->factory ?? new $factory($this);
    }

    /**@return RepositoryBase*/
    public function repository()
    {
        $class = $this->repositoryClass();
        return $this->repository = $this->repository ?? new $class($this);
    }
}
