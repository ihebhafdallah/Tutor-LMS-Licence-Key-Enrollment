<?php

class Tutor_Licence_Key_Admin
{

    public function __construct()
    {
        add_action('admin_menu', [$this, 'register_menu']);
        add_action('admin_post_generate_licence_keys', [$this, 'handle_form']);
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
}
