<?php
/**
 * Created by PhpStorm.
 * User: OpenUser
 * Date: 31.07.2018
 * Time: 16:40
 */

namespace App\Articles;

use Illuminate\Database\Eloquent\Collection;

interface ArticlesRepository
{
    public function search(string $query = ""): Collection;
}
