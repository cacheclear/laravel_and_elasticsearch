<?php
/**
 * Created by PhpStorm.
 * User: OpenUser
 * Date: 31.07.2018
 * Time: 16:42
 */

namespace App\Articles;

use Illuminate\Database\Eloquent\Collection;
use App\Article;

class EloquentArticlesRepository implements ArticlesRepository
{
    public function search(string $query = ""): Collection
    {
        return Article::where('body', 'like', "%{$query}%")
            ->orWhere('title', 'like', "%{$query}%")
            ->get();
    }

}
