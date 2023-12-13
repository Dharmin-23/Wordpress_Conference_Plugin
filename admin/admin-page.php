<?php
// Include WordPress core functions
require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

// Include the file with database-related functions
require_once(plugin_dir_path(__FILE__) . '../includes/DB_file.php');

// Add an admin menu item
function conference_registration_menu() {
    add_menu_page(
        'Conference Registration',
        'Conference Registration',
        'manage_options',
        'conference-registration',
        'conference_registration_admin_page'
    );
}
add_action('admin_menu', 'conference_registration_menu');

// Admin page content
function conference_registration_admin_page() {
    ?>
    <div class="wrap">
        <h1>Conference Registration</h1>

        <!-- Form for event details -->
        <form method="post" action="">
            <h2>Event Details</h2>
            <label for="event_name">Event Name:</label>
            <input type="text" name="event_name" id="event_name" required>

            <label for="start_date">Start Date:</label>
            <input type="date" name="start_date" id="start_date" required>

            <label for="end_date">End Date:</label>
            <input type="date" name="end_date" id="end_date" required>

            <!-- Form for fee structure -->
            <h2>Fee Structure</h2>
            <table>
                <thead>
                    <tr>
                        <th>Fee Type</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><input type="text" name="fee_type[]" required></td>
                        <td><input type="number" name="fee_amount[]" required></td>
                    </tr>
                    <!-- Additional rows can be added dynamically with JavaScript -->
                </tbody>
            </table>

            <button type="button" id="add_fee_row">Add Fee</button>

            <br><br>
            <input type="submit" name="submit_event" value="Save Event">
        </form>
    </div>

    <script>
        // JavaScript to dynamically add fee rows
        document.getElementById('add_fee_row').addEventListener('click', function() {
            var tableBody = document.querySelector('table tbody');
            var newRow = tableBody.insertRow(tableBody.rows.length);
            newRow.innerHTML = '<td><input type="text" name="fee_type[]" required></td>' +
                               '<td><input type="number" name="fee_amount[]" required></td>';
        });
    </script>
    <?php
}

// Process form submission
if (isset($_POST['submit_event'])) {
    // Validate and sanitize form data
    $event_name = sanitize_text_field($_POST['event_name']);
    $start_date = sanitize_text_field($_POST['start_date']);
    $end_date = sanitize_text_field($_POST['end_date']);
    $fee_types = array_map('sanitize_text_field', $_POST['fee_type']);
    $fee_amounts = array_map('floatval', $_POST['fee_amount']);

    // Save event details and fee structure to the database
    save_event_details($event_name, $start_date, $end_date);
    save_fee_structure($fee_types, $fee_amounts);
}
