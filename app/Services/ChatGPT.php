<?php

namespace App\Services;

use OpenAI;

class ChatGPT
{
  public static function sendMessage()
  {
    $client = OpenAI::client($_ENV['OPENAI_KEY']);

    $response = $client->threads()->createAndRun(
      [
        'assistant_id' => 'asst_gxzBkD1wkKEloYqZ410pT5pd',
        'thread' => [
          'messages' =>
          [
            [
              'role' => 'user',
              'content' => 'Explain deep learning to a 5 year old.',
            ],
          ],
        ],
      ],
    );

    // $result = $client->chat()->create([
    //     'model' => 'gpt-4',
    //     'messages' => [
    //         ['role' => 'user', 'content' => 'Hello!'],
    //     ],
    // ]);

    // echo $result->choices[0]->message->content; // Hello! How can I assist you today?
  }

  public static function loadMessage(string $threadId)
  {
    $client = OpenAI::client($_ENV['OPENAI_KEY']);

    $messageList = $client->threads()->messages()->list($threadId, [
      'limit' => 20,
    ]);

    return self::formatList($messageList);
  }

  public static function deleteThread(string $threadId)
  {
    $client = OpenAI::client($_ENV['OPENAI_KEY']);

    $response = $client->threads()->delete($threadId);

    return $response;
  }

  private static function formatList($messageList)
  {
    $messageListNew = [];
    foreach ($messageList->data as $key => $row) {
      $dataRow = [];
      $dataRow['role'] = $row->role;
      $dataRow['created_at'] = $row->createdAt;
      $dataRow['message'] = $row->content[0]->text->value ?? '';
      array_push($messageListNew, $dataRow);
    }
    return $messageListNew;
  }
}
