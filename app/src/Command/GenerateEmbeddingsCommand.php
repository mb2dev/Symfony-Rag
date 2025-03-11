<?php

namespace App\Command;

use App\Factory\ElasticSearchClientFactory;
use App\Factory\OllamaConfigFactory;
use Exception;
use LLPhant\Embeddings\Document;
use LLPhant\Embeddings\EmbeddingGenerator\Ollama\OllamaEmbeddingGenerator;
use LLPhant\Embeddings\VectorStores\Elasticsearch\ElasticsearchVectorStore;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'GenerateEmbeddings',
    description: 'Generate Embeddings to populate ElasticsearchData',
)]
class GenerateEmbeddingsCommand extends Command
{
    private ElasticSearchClientFactory $esClientFactory;
    private OllamaConfigFactory $ollamaConfigFactory;


    public function __construct(
        ElasticSearchClientFactory $elasticSearchClientFactory,
        OllamaConfigFactory $ollamaConfigFactory
    ) {
        parent::__construct();
        $this->esClientFactory = $elasticSearchClientFactory;
        $this->ollamaConfigFactory = $ollamaConfigFactory;
    }

    /**
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title("Begin to generate Embeddings");

        $io->title("Open the JSON Document");
        $jsonFile =  file_get_contents(__DIR__ . '/../../public/intervention.json');
        if (! $jsonFile) {
            return Command::FAILURE;
        }

        $documents = json_decode($jsonFile, true);
        $io->success("Document Loaded");

        $io->section("Split JSON and Create LLPhant Document for each Object");
        $chunkNumber = 0;
        $splitDocuments = [];
        foreach ($documents as $item) {
            $newDocument = new  Document();
            $data = json_encode($item);
            if (false != $data) {
                $newDocument->id = $item['id'];
                $newDocument->content = $data;
                $newDocument->hash = hash('sha256', $data);
                $newDocument->sourceType = 'json';
                $newDocument->sourceName = "intervention";
                $newDocument->chunkNumber = $chunkNumber;
                $chunkNumber++;
                $splitDocuments[] = $newDocument;
            }
        }
        $io->success("Split Finished");

        $io->section("Generate Embeddings");
        $config = $this->ollamaConfigFactory->getConfig();

        $embeddingGenerator = new OllamaEmbeddingGenerator($config);
        $embeddedDocuments = $embeddingGenerator->embedDocuments($splitDocuments);

        $io->success("Embeddings generated");

        $io->section("Save Embeddings into ES");
        $es = $this->esClientFactory->getClient();

        $elasticVectorStore = new ElasticsearchVectorStore($es, 'intervention');
        $elasticVectorStore->addDocuments($embeddedDocuments);


        $io->success("Embeddings saved to ES");

        return Command::SUCCESS;
    }
}
