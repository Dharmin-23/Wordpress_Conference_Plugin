<?php

require_once(plugin_dir_path(__FILE__) . 'database.php');


function conference_registration_form_shortcode($atts) {
    ob_start(); 

?>
    <div class="conference-registration-form">
        <h2>Conference Registration Form</h2>
        <form method="post" action="">

            <label for="full_name">Full Name:</label>
            <input type="text" name="full_name" id="full_name" required>

            <label for="email">Email:</label>
            <input type="email" name="email" id="email" required>

            <label for="event_selection">Select Event:</label>
            <?php
            $events = get_available_events();

            if ($events) {
                echo '<select name="event_selection" id="event_selection" required>';
                foreach ($events as $event) {
                    echo '<option value="' . esc_attr($event->id) . '">' . esc_html($event->event_name) . '</option>';
                }
                echo '</select>';
            } else {
                echo '<p>No events available.</p>';
            }
            ?>
            <input type="submit" name="submit_registration" value="Register">
        </form>
    </div>
    <?php

    return ob_get_clean(); 
}

//Shortcode to put in wordpress page
add_shortcode('conference_registration_form', 'conference_registration_form_shortcode');

// Handling form submission
if (isset($_POST['submit_registration'])) {
    $full_name = sanitize_text_field($_POST['full_name']);
    $email = sanitize_email($_POST['email']);
    $event_id = intval($_POST['event_selection']);
    save_conference_registration($full_name, $email, $event_id);
    echo '<p>Registration successful!</p>';
}
