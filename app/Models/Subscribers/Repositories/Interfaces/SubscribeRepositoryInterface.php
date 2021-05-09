<?php

namespace App\Models\Subscribers\Repositories\Interfaces;

use Jsdecena\Baserepo\BaseRepositoryInterface;
use App\Models\Subscribers\Subscribe;
use Illuminate\Support\Collection;

interface SubscribeRepositoryInterface extends BaseRepositoryInterface
{
    public function listSubscribes(string $order = 'id', string $sort = 'desc', $except = []) : Collection;

    public function createSubscribe(array $params) : Subscribe;

    public function findSubscribeById(int $id) : Subscribe;

    public function updateSubscribe(array $params): bool;

    public function deleteSubscribe() : bool;
}
