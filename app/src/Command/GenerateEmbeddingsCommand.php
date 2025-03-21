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
                $newDocument->content = $this->formatEmbeddingContent($item);
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

    /**
     * @param array{
     *     id: int,
     *     intervention_type: string,
     *     equipment: string,
     *     technician: string,
     *     intervention_date: string,
     *     duration: int,
     *     priority: string,
     *     status: string,
     *     description: string,
     *     used_parts: array<array{quantity: int, part: string}>,
     *     comments: string
     * } $item
     */
    private function formatEmbeddingContent(array $item): string
    {
        return "Intervention ID: {$item['id']}\n" .
            "Type: {$item['intervention_type']}\n" .
            "Equipment: {$item['equipment']}\n" .
            "Technician: {$item['technician']}\n" .
            "Date: {$item['intervention_date']}\n" .
            "Duration: {$item['duration']} min\n" .
            "Priority: {$item['priority']}\n" .
            "Status: {$item['status']}\n" .
            "Description: {$item['description']}\n" .
            "Parts Used: " .
            implode(", ", array_map(
                fn(array $p): string => "{$p['quantity']}x {$p['part']}",
                $item['used_parts']
            )) . "\n" .
            "Comments: {$item['comments']}";
    }
}
