<?php

namespace Modules\Ichat\Repositories;

use Modules\Core\Repositories\BaseRepository;

interface ConversationRepository extends BaseRepository
{
    /**
   * Return the latest x blog posts
   * @param object $params
   * @return Collection
   */
  public function getItemsBy($params);

    /**
   * Return the latest x blog posts
   * @param object $params
   * @param object $params
   * @return Collection
   */
  public function getItem($criteria, $params);

    /**
   * Return the latest x blog posts
   * @param object $data
   * @return Collection
   */
  public function create($data);

    /**
   * Return the latest x blog posts
   * @param String $criteria
   * @param object $data
   * @param object $params
   * @return Collection
   */
  public function updateBy($criteria, $data, $params);

    /**
   * Return the latest x blog posts
   * @param String $criteria
   * @param object $params
   * @return Collection
   */
  public function deleteBy($criteria, $params);
}
