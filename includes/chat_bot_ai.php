<div class="openai_chatGPT">
    <h4><?php echo __('RND AI Chat Bot', 'viral-content-with-ai'); ?></h4>
    <form id="user-input-form" method="post">
        <label><?php echo __('Feel free to ask anything to AI Chat Bot', 'viral-content-with-ai'); ?></label>
        <textarea class="user_textarea_input" id="user_input_output"></textarea>
        <div class="select_boxes_options">
            <label for="language"><?php echo __('Language', 'viral-content-with-ai'); ?>:</label>
            <select name="language" id="language">
                <?php foreach ($this->languages as $lang): ?>
                <option value="<?php echo esc_attr($lang); ?>"><?php echo esc_attr($lang); ?></option>
                <?php endforeach;?>

            </select>
            <label for="writingStyle"><?php echo __('Writing Style', 'viral-content-with-ai'); ?>:</label>
            <select name="writingStyle" id="writingStyle">
                <?php foreach ($this->writingStyles as $style): ?>
                <option value="<?php echo esc_attr($style); ?>"><?php echo esc_attr($style); ?></option>
                <?php endforeach;?>
            </select>
            <label for="writingTone"><?php echo __('Writing Tone', 'viral-content-with-ai'); ?>:</label>
            <select name="writingTone" id="writingTone">
                <?php foreach ($this->writingTones as $tone): ?>
                <option value="<?php echo esc_attr($tone); ?>"><?php echo esc_attr($tone); ?></option>
                <?php endforeach;?>
            </select>
        </div>
        <div class="speech_withText_convert" id="speech_convert">
            <input class="user_input" name="user_input_txt" id="user_input"
                value="<?php echo esc_attr($params['prompt_default']); ?>" size="80" autocomplete="off" autofocus>
        </div>
        <div id="show_loader" style="display:none"><img
                src="<?php echo Viral_Content_With_Ai_Url . 'assets/images/loader.gif'; ?>" /></div>
        <div id="show_btn">
            <input id="submit_user_query" type="button" value="<?php echo __('Submit', 'viral-content-with-ai'); ?>">
            <input id="reset_txtarea" type="button" value="<?php echo __('Reset', 'viral-content-with-ai'); ?>">
        </div>


    </form>
</div>

<script>
const searchForm = document.querySelector("#user-input-form");
const guesses = document.querySelector("#speech_convert");
const searchFormInput = searchForm.querySelector("input");
// The speech recognition interface lives on the browser’s window object
const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition; // if none exists -> undefined

if (SpeechRecognition) {
    console.log("Your Browser supports speech Recognition");

    const recognition = new SpeechRecognition();
    recognition.continuous = true;

    guesses.insertAdjacentHTML("beforeend", '<button type="button"><i class="fas fa-microphone"></i></button>');


    const micBtn = searchForm.querySelector("button");
    const micIcon = micBtn.firstElementChild;

    micBtn.addEventListener("click", micBtnClick);

    function micBtnClick() {
        if (micIcon.classList.contains("fa-microphone")) { // Start Voice Recognition
            recognition.start(); // First time you have to allow access to mic!
        } else {
            recognition.stop();
        }
    }

    recognition.addEventListener("start", startSpeechRecognition); // <=> recognition.onstart = function() {...}
    function startSpeechRecognition() {
        micIcon.classList.remove("fa-microphone");
        micIcon.classList.add("fa-microphone-slash");
        searchFormInput.focus();
        console.log("Voice activated, SPEAK");
    }

    recognition.addEventListener("end", endSpeechRecognition); // <=> recognition.onend = function() {...}
    function endSpeechRecognition() {
        micIcon.classList.remove("fa-microphone-slash");
        micIcon.classList.add("fa-microphone");
        searchFormInput.focus();
        console.log("Speech recognition service disconnected");
    }

    recognition.addEventListener("result",
    resultOfSpeechRecognition); // <=> recognition.onresult = function(event) {...} - Fires when you stop talking
    function resultOfSpeechRecognition(event) {
        const current = event.resultIndex;
        const transcript = event.results[current][0].transcript;

        if (transcript.toLowerCase().trim() === "stop recording") {
            recognition.stop();
        } else if (!searchFormInput.value) {
            searchFormInput.value = transcript;
        } else {
            if (transcript.toLowerCase().trim() === "go") {
                searchForm.submit();
            } else if (transcript.toLowerCase().trim() === "reset input") {
                searchFormInput.value = "";
            } else {
                searchFormInput.value = transcript;
            }
        }

    }


} else {
    console.log("Your Browser does not support speech Recognition");
}
</script>