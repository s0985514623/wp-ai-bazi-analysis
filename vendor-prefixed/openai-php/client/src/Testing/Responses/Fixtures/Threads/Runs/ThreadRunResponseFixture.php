<?php

namespace R2WpBaziPlugin\vendor\OpenAI\Testing\Responses\Fixtures\Threads\Runs;

final class ThreadRunResponseFixture
{
    public const ATTRIBUTES = [
        'id' => 'run_4RCYyYzX9m41WQicoJtUQAb8',
        'object' => 'thread.run',
        'created_at' => 1_699_621_735,
        'assistant_id' => 'asst_EopvUEMh90bxkNRYEYM81Orc',
        'thread_id' => 'thread_EKt7MjGOC6bwKWmenQv5VD6r',
        'status' => 'queued',
        'started_at' => null,
        'expires_at' => 1_699_622_335,
        'cancelled_at' => null,
        'failed_at' => null,
        'completed_at' => null,
        'last_error' => null,
        'model' => 'gpt-4',
        'instructions' => 'You are a personal math tutor. When asked a question, write and run Python code to answer the question.',
        'tools' => [
            [
                'type' => 'code_interpreter',
            ],
        ],
        'metadata' => [],
        'usage' => [
            'prompt_tokens' => 5,
            'completion_tokens' => 7,
            'total_tokens' => 12,
        ],
        'incomplete_details' => null,
        'temperature' => 1,
        'top_p' => 1,
        'max_prompt_tokens' => 600,
        'max_completion_tokens' => 500,
        'truncation_strategy' => [
            'type' => 'auto',
            'last_messages' => null,
        ],
        'tool_choice' => 'none',
        'response_format' => 'auto',
    ];
}
