<?php

namespace Modules\ModuleBase\Repository;

use Modules\ModuleBase\Domain\DomainBase;

abstract class RepositoryBaseAbstract implements RepositoryInterface
{
    /**@var DomainBase*/
    protected $domain;

    public function __construct($domain)
    {
        $this->domain = $domain;
    }
}
