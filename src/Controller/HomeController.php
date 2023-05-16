<?php
namespace App\Controller;

use App\Services\OpenAIService;
use App\Entity\Message;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class HomeController extends AbstractController
{
	private OpenAIService $openAIService;
	private EntityManagerInterface $entityManager;

	public function __construct(OpenAIService $openAIService, EntityManagerInterface $entityManager)
	{
		$this->openAIService = $openAIService;
		$this->entityManager = $entityManager;
	}

	#[Route('/', name: 'app_home')]
	public function index(Request $request): Response
	{
		// traitement de la nouvelle question
		if ($request->request->get('question')) {

			$answer = $this->chatWithGPT($request->request->get('question'));
			$messageAssistant = new Message;

			$messageAssistant
				->setContent($answer['choices'][0]['message']['content'])
				->setRole('assistant');

			$this->entityManager->persist($messageAssistant);
			$this->entityManager->flush();
		}

		// Affichage de la conversation
		$messages = $this->entityManager
			->getRepository(Message::class)
			->findBy([], ['id' => 'DESC'], 8);

		// dd($messages);

		return $this->render('home/index.html.twig', [
			'controller_name' => 'HomeController',
			'messages' => array_reverse($messages),
		]);
	}

	#[Route('/clear', name: 'app_clear')]
	public function clear()
	{
		$repository = $this->entityManager->getRepository(Message::class);
		$messages = $repository->findAll();

		foreach ($messages as $message) {
			$this->entityManager->remove($message);
		}

		$this->entityManager->flush();

		return $this->redirectToRoute('app_home');
	}

	private function chatWithGPT(string $question)
	{
		// Enregistrement de la nouvelle question en bdd
		$messageUser = new Message;
		$messageUser
			->setRole('user')
			->setContent($question);

		$this->entityManager->persist($messageUser);
		$this->entityManager->flush();

		// dd($messages);

		// Affichage de la conversation
		$messages = $this->entityManager
			->getRepository(Message::class)
			->findBy([], ['id' => 'ASC']);

		// Pré-prompt de personnalisation
		$prompt = [['role' => 'system', 'content' => "parle en tutoyant"],];
		// $prompt = [['role' => 'system', 'content' => "parles vulgaire avec plein de gros mots et soit aigri"],];

		// Ajout de la conversation
		foreach ($messages as $message) {
			$prompt[] = ['role' => $message->getRole(), 'content' => $message->getContent()];
		}

		// dd($prompt);

		// Retour du traitement de la conversation par GPT
		return $this->openAIService->response($prompt);
	}
}