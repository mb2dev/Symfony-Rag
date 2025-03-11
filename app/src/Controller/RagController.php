<?php

namespace App\Controller;

use App\Factory\ElasticSearchClientFactory;
use App\Factory\OllamaConfigFactory;
use LLPhant\Chat\OllamaChat;
use LLPhant\Embeddings\EmbeddingGenerator\Ollama\OllamaEmbeddingGenerator;
use LLPhant\Embeddings\VectorStores\Elasticsearch\ElasticsearchVectorStore;
use LLPhant\Query\SemanticSearch\QuestionAnswering;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class RagController extends AbstractController
{
    private ElasticSearchClientFactory $esClientFactory;
    private OllamaConfigFactory $ollamaConfigFactory;

    public function __construct(ElasticSearchClientFactory $esClientFactory, OllamaConfigFactory $ollamaConfigFactory)
    {
        $this->esClientFactory = $esClientFactory;
        $this->ollamaConfigFactory = $ollamaConfigFactory;
    }

    #[Route('/', name: 'app_rag')]
    public function index(): JsonResponse
    {

        $es = $this->esClientFactory->getClient();
        $config = $this->ollamaConfigFactory->getConfig();
        $chat = new OllamaChat($config);
        $embeddingGenerator = new OllamaEmbeddingGenerator($config);

        $elasticVectorStore = new ElasticsearchVectorStore($es, 'intervention');

        #RAG
        $qa = new QuestionAnswering(
            $elasticVectorStore,
            $embeddingGenerator,
            $chat
        );

        $answer = $qa->answerQuestion("Ask a question", 4);

        return $this->json([
            'message' => 'Welcome to your new controller!',
            "answer" => $answer,
            'path' => 'src/Controller/RagController.php',
        ]);
    }

}
