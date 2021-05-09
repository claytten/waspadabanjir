<?php

namespace App\Models\Subscribers\Repositories;

use Jsdecena\Baserepo\BaseRepository;
use App\Models\Subscribers\Subscribe;
use App\Models\Subscribers\Exceptions\SubscribeNotFoundException;
use App\Models\Subscribers\Repositories\Interfaces\SubscribeRepositoryInterface;
use App\Models\Tools\UploadableTrait;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;
use Illuminate\Database\QueryException;

class SubscribeRepository extends BaseRepository implements SubscribeRepositoryInterface
{
    use UploadableTrait;
    /**
     * SubscribeRepository constructor.
     *
     * @param Subscribe $subscribe
     */
    public function __construct(Subscribe $subscribe)
    {
        parent::__construct($subscribe);
        $this->model = $subscribe;
    }

    /**
     * List all the subscribes
     *
     * @param string $order
     * @param string $sort
     *
     * @return Collection
     */
    public function listSubscribes(string $order = 'id', string $sort = 'desc', $except = []) : Collection
    {
        return $this->model->orderBy($order, $sort)->get()->except($except);
    }

    /**
     * Create the Subscribe
     *
     * @param array $data
     *
     * @return Subscribe
     */
    public function createSubscribe(array $data): Subscribe
    {
        try {
            if(substr($data['phone'],0,3) == '+62') {
                $data['phone'] = preg_replace("/^0/", "+62", $data['phone']);
            } else if(substr($data['phone'],0,1) == '0') {
                $data['phone'] = preg_replace("/^0/", "+62", $data['phone']);
            } else {
                $data['phone'] = "+62".$data['phone'];
            }
            return $this->model->create($data);
        } catch (QueryException $e) {
            throw new SubscribeNotFoundException($e);
        }
    }

    /**
     * Find the subscribe by id
     *
     * @param int $id
     *
     * @return Subscribe
     */
    public function findSubscribeById(int $id): Subscribe
    {
        try {
            return $this->findOneOrFail($id);
        } catch (ModelNotFoundException $e) {
            throw new SubscribeNotFoundException;
        }
    }

    /**
     * Update subscribe
     *
     * @param array $params
     *
     * @return bool
     */
    public function updateSubscribe(array $params): bool
    {
        if(substr($params['phone'],0,3) == '+62') {
            $params['phone'] = preg_replace("/^0/", "+62", $params['phone']);
        } else if(substr($params['phone'],0,1) == '0') {
            $params['phone'] = preg_replace("/^0/", "+62", $params['phone']);
        } else {
            $params['phone'] = "+62".$params['phone'];
        }
        return $this->model->update($params);
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function deleteSubscribe() : bool
    {
        return $this->delete();
    }
}
