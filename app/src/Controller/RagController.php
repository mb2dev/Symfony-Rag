<?php

namespace App\Controller;

use Elastic\Elasticsearch\ClientBuilder;
use LLPhant\Chat\OpenAIChat;
use LLPhant\Embeddings\EmbeddingGenerator\OpenAI\OpenAI3SmallEmbeddingGenerator;
use LLPhant\Embeddings\VectorStores\Elasticsearch\ElasticsearchVectorStore;
use LLPhant\Query\SemanticSearch\QuestionAnswering;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class RagController extends AbstractController
{
    #[Route('/', name: 'app_rag')]
    public function index(): JsonResponse
    {
//        dump($_ENV);exit;
        $es = (new ClientBuilder())::create()
            ->setHosts(["http://elasticsearch:9200"])
            ->build();


       // $chat = new GPT4Turbo();
        $embeddingGenerator = new OpenAI3SmallEmbeddingGenerator();

        $elasticVectorStore = new ElasticsearchVectorStore($es);


        #RAG
        $qa = new QuestionAnswering(
            $elasticVectorStore,
            $embeddingGenerator,
            new OpenAIChat()
        );

        $answer = $qa->answerQuestion("A quoi servent les fichiers XLIFF ?");


        return $this->json([
            'message' => 'Welcome to your new controller!',
            "nnswer" => $answer,
            'path' => 'src/Controller/RagController.php',
        ]);
    }
}
