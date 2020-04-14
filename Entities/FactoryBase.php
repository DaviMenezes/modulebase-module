<?php

namespace Modules\ModuleBase\Entites;

use Modules\ModuleBase\Domain\DomainBase;

class FactoryBase
{
    /**@var DomainBase*/
    protected $domain;

    public function __construct($domain)
    {
        $this->domain = $domain;
    }

    public function fill($data)
    {
        $this->domain->repository()->fill($data);
        return $this->domain->repository();
    }
}
