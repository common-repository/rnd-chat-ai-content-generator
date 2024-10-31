<?php
/**
 * Plugin Name:     Viral content with AI
 * Plugin URI:      rndexperts.com
 * Description:     This plugin can be used to Embed Open AI Chat GPT. You can place shortcode [vcwai_chat_gpt_embed] anywhere on your templates, pages or posts.
 * Author:          Rnd Experts
 * Author URI:      rndexperts
 * Text Domain:     viral-content-with-ai
 * Domain Path:     /languages
 * Version:         2.0
 *
 * @package         Viral_Content_With_Ai
 */

define('Viral_Content_With_Ai_Url', plugin_dir_url(__FILE__));
define('Viral_Content_With_Ai_Path', plugin_dir_path(__FILE__));
require_once( Viral_Content_With_Ai_Path . 'classes/init.php');
$rndopenai_main = new VcwAI_main();
