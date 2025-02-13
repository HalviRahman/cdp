<?php

namespace App\Repositories;

use App\Models\Proposal;

class ProposalRepository extends Repository
{

    /**
     * constructor method
     *
     * @return void
     */
    public function __construct()
    {
        $this->model = new Proposal();
    }
}
