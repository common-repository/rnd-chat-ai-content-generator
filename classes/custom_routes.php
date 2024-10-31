<?php
class VcwAI_Custom_Routes
{
    private $namespace = 'vcwai/v1';
    private $api_key;
    private $prompt = '';
    private $temperature = 0.7;
    private $max_tokens = 2048;
    private $top_p = 1;
    private $frequency_penalty = 0;
    private $presence_penalty = 0;
    private $model = 'text-davinci-003';
    private $follow_up_prompt = '';
    private $separator = '\n';

    public function __construct()
    {
        $this->setModel('text-davinci-003');
        add_action('rest_api_init', array($this, 'register_routes'));
    }

    public function register_routes()
    {
        register_rest_route($this->namespace, '/get_prompts', array(
            'methods' => 'POST',
            'callback' => array($this, 'get_prompts'),
            'permission_callback' => '__return_true',
        ));

    }

    public function setModel($model)
    {
        $this->model = $model;
    }

    public function addText($text)
    {

        $this->prompt .= $text . ' ';
    }

    public function addPlaceholder($text, $type = 'text')
    {
        $this->prompt .= '\"' . $text . '\" ';
    }

    public function setFollowUpPrompt($prompt)
    {
        $this->follow_up_prompt = $prompt;
    }

    public function getPrompt()
    {
        return $this->prompt . $this->separator . $this->follow_up_prompt;
    }

    public function getFollowUpPrompt()
    {
        return $this->follow_up_prompt;
    }

    public function getPromptAndModel()
    {
        return array('prompt' => $this->prompt, 'model' => $this->model, 'follow_up_prompt' => $this->follow_up_prompt);
    }

    public function get_prompts(WP_REST_Request $request)
    {
        $api_key = $this->api_key = get_option('openai_api_key') ? get_option('openai_api_key') : '';
        if ($api_key) {
            try {
                $params = $request->get_json_params();
                $prompt = sanitize_text_field($_REQUEST['prompt']);
                $language = sanitize_text_field($_REQUEST['language']);
                $writingStyle = sanitize_text_field($_REQUEST['writingStyle']);
                $writingTone = sanitize_text_field($_REQUEST['writingTone']);
                $this->addPlaceholder($prompt);
                $this->addText(' in ' . $language . '.');
                $this->addText(' Style: ' . $writingStyle . '.');
                $this->addText(' Tone: ' . $writingTone . '.');
                $final_prompt = $this->getPrompt();
                $answer = $this->_call_openai_api();
                if (isset($answer['choices'])) {
                    return new WP_REST_Response(['success' => true, 'data' => $answer['choices'][0]['text'], 'usage' => $answer['usage'], 'prompt' => $final_prompt], 200);
                } else {
                    if(isset($answer['message'])) {
                        return new WP_REST_Response(['success' => false, 'message' => $answer['message'], 'prompt' => $final_prompt], 500);
                    }
                    return new WP_REST_Response(['success' => true, 'data' => '', 'usage' => [], 'prompt' => $final_prompt], 200);
                }
            } catch (Exception $e) {
                return new WP_REST_Response(['success' => false, 'message' => $e->getMessage(), 'prompt' => $final_prompt], 500);
            }
        } else {
            return new WP_REST_Response(['success' => true, 'data' => 'OpenAI API keys is not configured.', 'usage' => [], 'prompt' => $final_prompt], 200);
        }
    }

    public function _call_openai_api()
    {
        $api_key = $this->api_key = get_option('openai_api_key') ? get_option('openai_api_key') : '';
        $url = 'https://api.openai.com/v1/completions';
        $data = array(
            'model' => $this->model,
            'prompt' => $this->getPrompt(),
            'temperature' => $this->temperature,
            'max_tokens' => $this->max_tokens,
            'top_p' => $this->top_p,
            'frequency_penalty' => $this->frequency_penalty,
            'presence_penalty' => $this->presence_penalty,
        );
        $args = array(
            'headers' => "Content-Type: application/json\r\n" . "Authorization: Bearer " . $api_key . "\r\n",
            'method' => 'POST',
            'body' => json_encode($data),
            'timeout' => 60,
            'sslverify' => false
        );
        try {
            $response = wp_remote_request($url, $args);
            if (is_wp_error($response)) {
                throw new Exception($response->get_error_message());
            }

            $result = wp_remote_retrieve_body($response);
            return json_decode($result, true);
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw new Exception('Error while calling OpenAI: ' . $e->getMessage());
        }
    }

}
