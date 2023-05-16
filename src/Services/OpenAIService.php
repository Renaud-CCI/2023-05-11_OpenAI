<?php

namespace App\Services;

use App\Repository\OpenAIServiceRepository;
use Doctrine\ORM\Mapping as ORM;
use OpenAI;

class OpenAIService
{

	public function response(array $prompt = ['role' => 'user', 'content' => 'test'])
	{
		$apiKey = $_ENV['API_KEY'];

		$client = OpenAI::client($apiKey);

		$response = $client->chat()->create([
			'model' => 'gpt-3.5-turbo',
			'messages' => $prompt
		]);

		// $response->id; // 'chatcmpl-6pMyfj1HF4QXnfvjtfzvufZSQq6Eq'
		// $response->object; // 'chat.completion'
		// $response->created; // 1677701073
		// $response->model; // 'gpt-3.5-turbo-0301'

		// foreach ($response->choices as $result) {
		// 	$result->index; // 0
		// 	$result->message->role; // 'assistant'
		// 	$result->message->content; // '\n\nHello there! How can I assist you today?'
		// 	$result->finishReason; // 'stop'
		// }

		// $response->usage->promptTokens; // 9,
		// $response->usage->completionTokens; // 12,
		// $response->usage->totalTokens; // 21

		return $response->toArray(); // ['id' => 'chatcmpl-6pMyfj1HF4QXnfvjtfzvufZSQq6Eq', ...]
	}
}