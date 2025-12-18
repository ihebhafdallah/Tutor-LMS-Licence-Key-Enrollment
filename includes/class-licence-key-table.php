<?php

class Tutor_Licence_Key_Table extends WP_List_Table
{

    public function get_columns()
    {
        return [
            'cb'          => '<input type="checkbox" />',
            //'id'          => __('ID', 'tutor-lms-licence-key-enrollment'),
            'course_id'   => __('Course', 'tutor-lms-licence-key-enrollment'),
            'licence_key' => __('Licence Key', 'tutor-lms-licence-key-enrollment'),
            'status'      => __('Status', 'tutor-lms-licence-key-enrollment'),
            'created_at'  => __('Created', 'tutor-lms-licence-key-enrollment'),
        ];
    }

    public function prepare_items()
    {
        $this->process_bulk_action();

        $per_page     = 10;
        $current_page = $this->get_pagenum();

        $this->items = Tutor_Licence_Key_Manager::get_keys($per_page, $current_page);

        $this->set_pagination_args([
            'total_items' => Tutor_Licence_Key_Manager::count_keys(),
            'per_page'    => $per_page,
        ]);

        $this->_column_headers = [
            $this->get_columns(),
            [],
            $this->get_sortable_columns(),
        ];
    }

    public function column_course_id($item)
    {
        $course_id = intval($item['course_id']);
        $title     = get_the_title($course_id);

        return $title
            ? esc_html($title)
            : '<em>' . esc_html__('Deleted course', 'tutor-lms-licence-key-enrollment') . '</em>';
    }

    public function column_default($item, $column_name)
    {
        return isset($item[$column_name])
            ? esc_html($item[$column_name])
            : '';
    }

    protected function extra_tablenav($which)
    {
        if ($which !== 'top') {
            return;
        }

        $selected_course = $_GET['filter_course'] ?? '';
        $selected_status = $_GET['filter_status'] ?? '';
?>
        <div class="alignleft actions">

            <!-- Existing filters -->
            <select name="filter_course">
                <option value=""><?php esc_html_e('All Courses', 'tutor-lms-licence-key-enrollment'); ?></option>
                <?php
                $courses = get_posts([
                    'post_type'   => 'courses',
                    'numberposts' => -1,
                ]);

                foreach ($courses as $course) {
                    printf(
                        '<option value="%d" %s>%s</option>',
                        $course->ID,
                        selected($selected_course, $course->ID, false),
                        esc_html($course->post_title)
                    );
                }
                ?>
            </select>

            <select name="filter_status">
                <option value=""><?php esc_html_e('All Statuses', 'tutor-lms-licence-key-enrollment'); ?></option>
                <option value="active" <?php selected($selected_status, 'active'); ?>>Active</option>
                <option value="sent" <?php selected($selected_status, 'sent'); ?>>Sent</option>
                <option value="expired" <?php selected($selected_status, 'expired'); ?>>Expired</option>
            </select>

            <?php submit_button(__('Filter'), '', 'filter_action', false); ?>

        </div>

        <!-- EXPORT FORM -->
        <div class="alignleft actions">
            <select name="export_status">
                <option value="">
                    <?php esc_html_e('Export: All Keys', 'tutor-lms-licence-key-enrollment'); ?>
                </option>
                <option value="active">
                    <?php esc_html_e('Active', 'tutor-lms-licence-key-enrollment'); ?>
                </option>
                <option value="sent">
                    <?php esc_html_e('Sent', 'tutor-lms-licence-key-enrollment'); ?>
                </option>
                <option value="expired">
                    <?php esc_html_e('Expired', 'tutor-lms-licence-key-enrollment'); ?>
                </option>
            </select>

            <input type="hidden" name="export_csv" value="1">

            <?php
            submit_button(
                __('Export CSV', 'tutor-lms-licence-key-enrollment'),
                'secondary',
                '',
                false
            );
            ?>
        </div>



<?php
    }


    protected function get_sortable_columns()
    {
        return [
            'id' => ['id', true],
        ];
    }

    public function column_cb($item)
    {
        return sprintf(
            '<input type="checkbox" name="licence_keys[]" value="%d" />',
            absint($item['id'])
        );
    }

    protected function get_bulk_actions()
    {
        return [
            'mark_active'  => __('Mark as Active', 'tutor-lms-licence-key-enrollment'),
            'mark_sent'    => __('Mark as Sent', 'tutor-lms-licence-key-enrollment'),
            'mark_expired' => __('Mark as Expired', 'tutor-lms-licence-key-enrollment'),
            'delete'    => __('Delete', 'tutor-lms-licence-key-enrollment'),
        ];
    }

    public function process_bulk_action()
    {
        $action = $this->current_action();

        if (empty($_REQUEST['licence_keys'])) {
            return;
        }

        check_admin_referer('bulk-' . $this->_args['plural']);

        $ids = array_map('absint', (array) $_REQUEST['licence_keys']);

        switch ($action) {
            case 'mark_active':
                Tutor_Licence_Key_Manager::update_status($ids, 'active');
                break;

            case 'mark_sent':
                Tutor_Licence_Key_Manager::update_status($ids, 'sent');
                break;

            case 'mark_expired':
                Tutor_Licence_Key_Manager::update_status($ids, 'expired');
                break;

            case 'delete':
                Tutor_Licence_Key_Manager::delete_keys($ids);
                break;
        }
    }

    public function column_status($item)
    {
        switch ($item['status']) {
            case 'sent':
                return '<span style="color:#2271b1;font-weight:600;">Sent to User</span>';
            case 'expired':
                return '<span style="color:#d63638;font-weight:600;">Expired</span>';
            default:
                return '<span style="color:#46b450;font-weight:600;">Active</span>';
        }
    }
}
