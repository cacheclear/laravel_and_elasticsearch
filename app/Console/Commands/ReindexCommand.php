<?php

namespace App\Console\Commands;

use App\Article;
use Elasticsearch\Client;
use Illuminate\Console\Command;

class ReindexCommand extends Command
{
    protected $name = "search:reindex";
    protected $description = "Indexes all articles to elasticsearch";
    private $search;

    public function __construct(Client $search)
    {
        parent::__construct();

        $this->search = $search;
    }

    public function handle()
    {
        $this->info('Indexing all articles. Might take a while...');

        foreach (Article::cursor() as $model)
        {
            $this->search->index([
                'index' => $model->getSearchIndex(),
                'type' => $model->getSearchType(),
                'id' => $model->id,
                'body' => $model->toSearchArray(),
            ]);

            // PHPUnit-style feedback
            $this->output->write('.');
        }

        $this->info("nDone!");
    }
}
