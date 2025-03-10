<?php

namespace App\Command;

use Elastic\Elasticsearch\ClientBuilder;
use LLPhant\Embeddings\DataReader\FileDataReader;
use LLPhant\Embeddings\DocumentSplitter\DocumentSplitter;
use LLPhant\Embeddings\EmbeddingGenerator\OpenAI\OpenAI3SmallEmbeddingGenerator;
use LLPhant\Embeddings\VectorStores\Elasticsearch\ElasticsearchVectorStore;
use LLPhant\Embeddings\VectorStores\FileSystem\FileSystemVectorStore;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'GenerateEmbeddings',
    description: 'Generate Embeddings to populate ElasticsearchData',
)]
class GenerateEmbeddingsCommand extends Command
{
    public function __construct()
    {
        parent::__construct();
    }

//    protected function configure(): void
//    {
//        $this
//            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
//            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
//        ;
//    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title("Begin to generate Embeddings");


        $io->title("Read the document");
        $dataReader = new FileDataReader(__DIR__ . '/../../public/best_practices.rst');
        $documents = $dataReader->getDocuments();

        $io->success("Document Loaded");

        $io->section("Cutting out the document");
        $splittedDocuments = DocumentSplitter::splitDocuments($documents, 500);

        $io->success("Document splitted");

        $io->section("Generate Embeddings");
        $embeddingGenerator = new OpenAI3SmallEmbeddingGenerator();
        $embeddedDocuments = $embeddingGenerator->embedDocuments($splittedDocuments);

        $io->section("Save Embeddings");
        $vectorStore = new FileSystemVectorStore();
        $vectorStore->addDocuments($embeddedDocuments);


        $io->section("Index all the embeddings to Elasticsearch");
        $es = (new ClientBuilder())::create()

            ->setHosts(["http://elasticsearch:9200"])
//            ->setApiKey($env['ES_LOCAL_API_KEY'])
            ->build();

        $elasticVectorStore = new ElasticsearchVectorStore($es);
        $elasticVectorStore->addDocuments($embeddedDocuments);


//        $arg1 = $input->getArgument('arg1');
//
//        if ($arg1) {
//            $io->note(sprintf('You passed an argument: %s', $arg1));
//        }
//
//        if ($input->getOption('option1')) {
//            // ...
//        }

        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return Command::SUCCESS;
    }
}
