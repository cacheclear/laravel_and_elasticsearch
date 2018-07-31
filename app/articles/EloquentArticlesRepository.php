<?php
/**
 * Created by PhpStorm.
 * User: OpenUser
 * Date: 31.07.2018
 * Time: 16:42
 */

namespace App\articles;

use Illuminate\Database\Eloquent\Collection;

class EloquentArticlesRepository implements ArticlesRepository
{
    public function search(string $query = ""): Collection
    {
        return Article::where('body', 'like', "%{$query}%")
            ->orWhere('title', 'like', "%{$query}%")
            ->get();
    }

}
