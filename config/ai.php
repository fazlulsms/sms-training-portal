<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Master Feature Switch
    |--------------------------------------------------------------------------
    | Set AI_FEATURE_ENABLED=true in .env to activate all AI features.
    | When false every call to OpenAIService returns a disabled error without
    | hitting the API.
    */
    'enabled' => env('AI_FEATURE_ENABLED', false),

    /*
    |--------------------------------------------------------------------------
    | OpenAI Credentials
    |--------------------------------------------------------------------------
    | The API key is read exclusively from the environment — never hard-code it.
    | All application code must reference config('ai.api_key'), never env()
    | directly, so the value is cached and never leaked in stack traces.
    */
    'api_key' => env('OPENAI_API_KEY'),
    'model'   => env('OPENAI_MODEL', 'gpt-4o-mini'),
    'timeout' => (int) env('OPENAI_TIMEOUT', 30),

    /*
    |--------------------------------------------------------------------------
    | Usage Limits
    |--------------------------------------------------------------------------
    */
    'daily_request_limit'  => (int) env('AI_DAILY_REQUEST_LIMIT', 100),
    'monthly_budget_usd'   => (float) env('AI_MONTHLY_BUDGET_USD', 10.00),

    /*
    |--------------------------------------------------------------------------
    | Cost Estimates (USD per 1 000 000 tokens)
    | Used for logging estimated_cost_usd. Update when OpenAI changes pricing.
    |--------------------------------------------------------------------------
    */
    'cost_per_million' => [
        'gpt-4o-mini'  => ['input' => 0.150,  'output' => 0.600],
        'gpt-4o'       => ['input' => 2.50,   'output' => 10.00],
        'gpt-4-turbo'  => ['input' => 10.00,  'output' => 30.00],
        'gpt-3.5-turbo'=> ['input' => 0.50,   'output' => 1.50],
    ],

    /*
    |--------------------------------------------------------------------------
    | Future AI Feature Modules (placeholders — not yet implemented)
    |--------------------------------------------------------------------------
    |
    | Training AI
    |   course_generator   — Generate full course outlines from a topic
    |   lesson_generator   — Generate lesson content (blocks, video scripts)
    |   quiz_generator     — Generate MCQ / T/F quiz questions
    |   case_study         — Generate training case studies
    |
    | Marketing AI
    |   facebook_content   — Generate Facebook post / ad copy
    |   linkedin_content   — Generate LinkedIn articles and posts
    |   website_content    — Generate SEO page content
    |
    | Learning AI
    |   ai_tutor           — Interactive learner Q&A inside lessons
    |   learning_assistant — Personalised study recommendations
    |
    */
    'features' => [
        'course_generator'  => false,
        'lesson_generator'  => false,
        'quiz_generator'    => false,
        'case_study'        => false,
        'facebook_content'  => false,
        'linkedin_content'  => false,
        'website_content'   => false,
        'ai_tutor'          => false,
        'learning_assistant'=> false,
    ],

];
