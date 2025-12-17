<?php

class Tutor_Licence_Key_Manager
{

    public static function generate_keys($course_id, $quantity)
    {
        global $wpdb;

        $table = $wpdb->prefix . 'tutor_licence_keys';

        for ($i = 0; $i < $quantity; $i++) {
            $wpdb->insert(
                $table,
                [
                    'course_id'   => absint($course_id),
                    'licence_key' => self::generate_license_key(),
                    'status'      => 'active',
                    'created_at'  => current_time('mysql'),
                ]
            );
        }
    }

    public static function get_keys($per_page, $page)
    {
        global $wpdb;

        $table  = $wpdb->prefix . 'tutor_licence_keys';
        $offset = ($page - 1) * $per_page;

        $where = 'WHERE 1=1';
        $args  = [];

        if (! empty($_GET['filter_course'])) {
            $where .= ' AND course_id = %d';
            $args[] = absint($_GET['filter_course']);
        }

        if (! empty($_GET['filter_status'])) {
            $where .= ' AND status = %s';
            $args[] = sanitize_text_field($_GET['filter_status']);
        }

        $allowed_orderby = ['created_at', 'id'];
        $order_by = 'created_at';
        $order    = 'DESC';

        if (! empty($_GET['orderby']) && in_array($_GET['orderby'], $allowed_orderby, true)) {
            $order_by = $_GET['orderby'];
        }

        if (! empty($_GET['order']) && in_array(strtoupper($_GET['order']), ['ASC', 'DESC'], true)) {
            $order = strtoupper($_GET['order']);
        }

        $sql = "SELECT * FROM $table
                $where
                ORDER BY $order_by $order
                LIMIT %d OFFSET %d";

        $args[] = $per_page;
        $args[] = $offset;

        return $wpdb->get_results(
            $wpdb->prepare($sql, $args),
            ARRAY_A
        );
    }

    public static function count_keys()
    {
        global $wpdb;

        $table = $wpdb->prefix . 'tutor_licence_keys';

        $where = 'WHERE 1=1';
        $args  = [];

        if (! empty($_GET['filter_course'])) {
            $where .= ' AND course_id = %d';
            $args[] = absint($_GET['filter_course']);
        }

        if (! empty($_GET['filter_status'])) {
            $where .= ' AND status = %s';
            $args[] = sanitize_text_field($_GET['filter_status']);
        }

        $sql = "SELECT COUNT(*) FROM $table $where";

        if (! empty($args)) {
            return (int) $wpdb->get_var($wpdb->prepare($sql, $args));
        }

        return (int) $wpdb->get_var($sql);
    }

    public static function generate_license_key()
    {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $key = [];

        for ($i = 0; $i < 4; $i++) {
            $segment = '';
            for ($j = 0; $j < 4; $j++) {
                $segment .= $characters[random_int(0, strlen($characters) - 1)];
            }
            $key[] = $segment;
        }

        return implode('-', $key);
    }

    public static function delete_keys(array $ids)
    {
        global $wpdb;

        if (empty($ids)) {
            return;
        }

        $table = $wpdb->prefix . 'tutor_licence_keys';

        $placeholders = implode(',', array_fill(0, count($ids), '%d'));

        $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM $table WHERE id IN ($placeholders)",
                $ids
            )
        );
    }

    public static function update_status(array $ids, string $status)
    {
        global $wpdb;

        if (empty($ids)) {
            return;
        }

        $table = $wpdb->prefix . 'tutor_licence_keys';
        $placeholders = implode(',', array_fill(0, count($ids), '%d'));

        $wpdb->query(
            $wpdb->prepare(
                "UPDATE $table SET status = %s WHERE id IN ($placeholders)",
                array_merge([$status], $ids)
            )
        );
    }

    public static function get_keys_for_export($status = '')
    {
        global $wpdb;
        $table = $wpdb->prefix . 'tutor_licence_keys';

        $sql = "SELECT * FROM {$table}";
        $params = [];

        if ($status !== '') {
            $sql .= " WHERE status = %s";
            $params[] = $status;
        }

        return $params
            ? $wpdb->get_results($wpdb->prepare($sql, $params), ARRAY_A)
            : $wpdb->get_results($sql, ARRAY_A);
    }
}
