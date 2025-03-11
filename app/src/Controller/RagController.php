<?php

namespace App\Controller;

use App\Factory\ElasticSearchClientFactory;
use App\Factory\OllamaConfigFactory;
use App\Form\QuestionFormType;
use Exception;
use LLPhant\Chat\OllamaChat;
use LLPhant\Embeddings\EmbeddingGenerator\Ollama\OllamaEmbeddingGenerator;
use LLPhant\Embeddings\VectorStores\Elasticsearch\ElasticsearchVectorStore;
use LLPhant\Exception\MissingParameterException;
use LLPhant\Query\SemanticSearch\QuestionAnswering;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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

    /**
     * @throws MissingParameterException
     * @throws Exception
     */
    #[Route('/', name: 'app_rag')]
    public function index(Request $request): Response
    {

        $form = $this->createForm(QuestionFormType::class);
        $form->handleRequest($request);

        $answer = null;
        if ($form->isSubmitted() && $form->isValid()) {
            $question = $form->get('question')->getData();

            $es = $this->esClientFactory->getClient();
            $config = $this->ollamaConfigFactory->getConfig();
            $chat = new OllamaChat($config);
            $embeddingGenerator = new OllamaEmbeddingGenerator($config);

            $elasticVectorStore = new ElasticsearchVectorStore($es, 'intervention');

            $qa = new QuestionAnswering(
                $elasticVectorStore,
                $embeddingGenerator,
                $chat
            );

            $answer = $qa->answerQuestion($question, 4);
        }

        return $this->render('rag.html.twig', [
            'form' => $form->createView(),
            'answer' => $this->cleanAnswer($answer),
        ]);
    }

    private function cleanAnswer(?string $answer): string
    {
        return preg_replace('/<think>.*<\/think>/s', '', $answer ?? '') ?? '';
    }
}
