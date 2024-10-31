<?php
class VcwAI_main
{
    public $api_key;
    public $custom_routes;
    public $languages = [
        'English',
        'Spanish',
        'French',
        'German',
        'Italian',
        'Portuguese',
        'Russian',
        'Japanese',
        'Chinese'
    ];

    public $writingStyles = [
        'Informative',
        'Descriptive',
        'Creative',
        'Narrative',
        'Persuasive',
        'Reflective',
        'Argumentative',
        'Analytical',
        'Evaluative',
        'Journalistic',
        'Technical',

    ];
    public $writingTones = [
        'Neutral',
        'Formal',
        'Assertive',
        'Cheerful',
        'Humorous',
        'Informal',
        'Inspirational',
        'Professional',
        'Confvalueent',
        'Emotional',
        'Persuasive',
        'Supportive',
        'Sarcastic',
        'Condescending',
        'Skeptical',
        'Narrative',
        'Journalistic',

    ];

    public function __construct()
    {
        $this->load_all_classes();
        add_action('admin_init', array($this, 'vcwai_settings'));
        add_action('admin_menu', array($this, 'vcwai_admin_menu'));
        add_action('wp_enqueue_scripts', array($this, 'vcwai_enqueue_front_script'));
        add_shortcode('vcwai_chat_gpt_embed', array($this, 'vcwai_form_shortcode'));

    }

    public function vcwai_enqueue_front_script($hook)
    {
        wp_register_style('vcwai-frontend-styles', Viral_Content_With_Ai_Url . 'assets/css/viral-content-with-ai-front.css');
        wp_register_script('vcwai-frontend-fontsome', Viral_Content_With_Ai_Url . 'assets/js/fontsome.js', array('jquery'), '1.0');
        wp_register_script('vcwai-frontend', Viral_Content_With_Ai_Url . 'assets/js/viral-content-with-ai-front.js', array('jquery','vcwai-frontend-fontsome'), '1.0');
        $nonce = wp_create_nonce('wp_rest');
        wp_localize_script(
            'vcwai-frontend',
            'vcwai_front',
            array(
            'rest_url' => get_rest_url(null, 'vcwai/v1/'),
            'get_prompts' => 'get_prompts',
            'nonce' => $nonce,
        )
        );
    }

    public function load_all_classes()
    {
        $this->custom_routes = new VcwAI_Custom_Routes();
    }

    // Register the admin menu page

    public function vcwai_admin_menu()
    {
        add_menu_page(
            __('OpenAI Settings', 'viral-content-with-ai'),
            __('OpenAI Settings', 'viral-content-with-ai'),
            'manage_options',
            'vcwai-api-settings',
            array($this, 'vcwai_admin_page'),
            '',
            6
        );
    }

    public function vcwai_form_shortcode($atts)
    {
        ob_start();
        $params = shortcode_atts(array(
            'prompt_default' => "Write an article about India ",
            'language' => 'English',
            'writingStyle' => 'Informative',
            'writingTone' => 'Neutral',

        ), $atts);
        wp_enqueue_style('vcwai-frontend-styles');
        wp_enqueue_script('vcwai-frontend');
        require_once Viral_Content_With_Ai_Path . 'includes/chat_bot_ai.php';
        return ob_get_clean();
    }

    // Create the admin page
    public function vcwai_admin_page()
    {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }
        ?>
    <div class="wrap">
        <h1><?php echo __('OpenAI API keys Settings', 'viral-content-with-ai'); ?></h1>
        <form method="post" action="options.php">
            <?php settings_fields('vcwai_settings');?>
            <?php do_settings_sections('vcwai_settings');?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><?php echo __('Enter OpenAI API Key', 'viral-content-with-ai'); ?></th>
                    <td><input type="text" name="openai_api_key" value="<?php echo esc_attr(get_option('openai_api_key')); ?>" size="80" /></td>
                </tr>
            </table>
            <?php submit_button();?>
        </form>
    </div>
    <?php
    }

    // Register the plugin settings

    public function vcwai_settings()
    {
        register_setting('vcwai_settings', 'openai_api_key', 'sanitize_text_field');
    }

}
