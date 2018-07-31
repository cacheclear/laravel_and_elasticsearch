<?php

namespace App\Articles;

use App\Article;
use Elasticsearch\Client;
use Illuminate\Database\Eloquent\Collection;

class ElasticsearchArticlesRepository implements ArticlesRepository
{
    private $search;

    public function __construct(Client $client) {
        $this->search = $client;
    }

    public function search(string $query = ""): Collection
    {
        $items = $this->searchOnElasticsearch($query);

        return $this->buildCollection($items);
    }

    private function searchOnElasticsearch(string $query): array
    {
        $instance = new Article;

        $items = $this->search->search([
            'index' => $instance->getSearchIndex(),
            'type' => $instance->getSearchType(),
            'body' => [
                'query' => [
                    'multi_match' => [
                        'fields' => ['title', 'body', 'tags'],
                        'query' => $query,
                    ],
                ],
            ],
        ]);

        return $items;
    }

    private function buildCollection(array $items): Collection
    {
        /**
         * The data comes in a structure like this:
         *
         * [
         *      'hits' => [
         *          'hits' => [
         *              [ '_source' => 1 ],
         *              [ '_source' => 2 ],
         *          ]
         *      ]
         * ]
         *
         * And we only care about the _source of the documents.
         */
        $hits = array_pluck($items['hits']['hits'], '_source') ?: [];

        $sources = array_map(function ($source) {
            // The hydrate method will try to decode this
            // field but ES gives us an array already.
            $source['tags'] = json_encode($source['tags']);
            return $source;
        }, $hits);

        // We have to convert the results array into Eloquent Models.

        /*
         * We opted for hydrating the models with the documents we got from Elasticsearch.
         * This is only possible because we are indexing the whole model as it came from the
         * database (our single source of truth) using the $article->toArray(). Although we
         * might gain some time with this, it might be limiting in another scenario. Another
         * way of doing this is doing a Article::find($ids) using the IDs that came in the
         * Elasticsearch documents. Since IDs are indexed, chances are it’s faster when you
         * perform the query/filtering on Elasticsearch and load the models from the database
         * than performing the whole query/filtering in the Database itself. Using find($ids)
         * instead of hydrate($sources would also allow you to change the schema in a way
         * that makes sense and facilitates your searches vs. fighting the schema to build
         * a more complex search.
         */

        return Article::hydrate($sources);
    }
}
