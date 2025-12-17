<?php

class Tutor_Licence_Key_Admin
{

    public function __construct()
    {
        add_action('admin_menu', [$this, 'register_menu']);
        add_action('admin_post_generate_licence_keys', [$this, 'handle_form']);
        add_action('admin_init', [$this, 'handle_export']);
    }

    public function register_menu()
    {
        add_submenu_page(
            'tutor',
            __('Licence Keys', 'tutor-lms-licence-key-enrollment'),
            __('Licence Keys', 'tutor-lms-licence-key-enrollment'),
            'manage_options',
            'tutor-licence-keys',
            [$this, 'render_page']
        );
    }

    public function render_page()
    {
        $table = new Tutor_Licence_Key_Table();
        $table->prepare_items();
?>
        <div class="wrap">
            <h1><?php echo esc_html__('Generate Licence Keys', 'tutor-lms-licence-key-enrollment'); ?></h1>

            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                <input type="hidden" name="action" value="generate_licence_keys">
                <?php wp_nonce_field('generate_licence_keys_nonce'); ?>

                <table class="form-table">
                    <tr>
                        <th><?php echo esc_html__('Course', 'tutor-lms-licence-key-enrollment'); ?></th>
                        <td><?php $this->courses_dropdown(); ?></td>
                    </tr>
                    <tr>
                        <th><?php echo esc_html__('Number of Keys', 'tutor-lms-licence-key-enrollment'); ?></th>
                        <td>
                            <select name="quantity">
                                <option value="5"><?php echo esc_html__('5', 'tutor-lms-licence-key-enrollment'); ?></option>
                                <option value="10"><?php echo esc_html__('10', 'tutor-lms-licence-key-enrollment'); ?></option>
                                <option value="25"><?php echo esc_html__('25', 'tutor-lms-licence-key-enrollment'); ?></option>
                                <option value="50"><?php echo esc_html__('50', 'tutor-lms-licence-key-enrollment'); ?></option>
                                <option value="100"><?php echo esc_html__('100', 'tutor-lms-licence-key-enrollment'); ?></option>
                            </select>
                        </td>
                    </tr>
                </table>

                <?php submit_button(__('Generate Keys', 'tutor-lms-licence-key-enrollment')); ?>
            </form>

            <hr>

            <h1><?php echo esc_html__('Licence Keys', 'tutor-lms-licence-key-enrollment'); ?></h1>

            <form method="get">
                <input type="hidden" name="page" value="tutor-licence-keys">
                <?php $table->display(); ?>
            </form>
        </div>
<?php
    }

    private function courses_dropdown()
    {
        $courses = get_posts([
            'post_type'   => 'courses',
            'numberposts' => -1,
        ]);

        echo '<select name="course_id" required>';
        foreach ($courses as $course) {
            echo sprintf(
                '<option value="%d">%s</option>',
                absint($course->ID),
                esc_html($course->post_title)
            );
        }
        echo '</select>';
    }

    public function handle_form()
    {
        check_admin_referer('generate_licence_keys_nonce');

        $course_id = intval($_POST['course_id']);
        $quantity  = intval($_POST['quantity']);

        Tutor_Licence_Key_Manager::generate_keys($course_id, $quantity);

        wp_redirect(admin_url('admin.php?page=tutor-licence-keys&generated=1'));
        exit;
    }

    public function handle_export()
    {
        if (empty($_GET['export_csv'])) {
            return;
        }

        if (! current_user_can('manage_options')) {
            wp_die(
                __('Permission denied', 'tutor-lms-licence-key-enrollment')
            );
        }

        $status = sanitize_text_field($_GET['export_status'] ?? '');

        $keys = Tutor_Licence_Key_Manager::get_keys_for_export($status);

        $filename = sprintf(
            /* translators: %s: date */
            __('licence-keys-%s.csv', 'tutor-lms-licence-key-enrollment'),
            date_i18n('Y-m-d')
        );

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $filename);

        $output = fopen('php://output', 'w');

        fputcsv($output, [
            __('ID', 'tutor-lms-licence-key-enrollment'),
            __('Course', 'tutor-lms-licence-key-enrollment'),
            __('Licence Key', 'tutor-lms-licence-key-enrollment'),
            __('Status', 'tutor-lms-licence-key-enrollment'),
            __('Created', 'tutor-lms-licence-key-enrollment'),
        ]);

        foreach ($keys as $key) {
            fputcsv($output, [
                $key['id'],
                get_the_title($key['course_id']),
                $key['licence_key'],
                $key['status'],
                $key['created_at'],
            ]);
        }

        fclose($output);
        exit;
    }
}
