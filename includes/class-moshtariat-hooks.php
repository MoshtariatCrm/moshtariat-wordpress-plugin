<?php
/**
 * Moshtariat Form & WooCommerce Hooks.
 */

defined('ABSPATH') || exit;

class Moshtariat_CRM_Hooks {

    private Moshtariat_CRM_API $api;

    public function __construct(Moshtariat_CRM_API $api) {
        $this->api = $api;
    }

    public function init(): void {
        // Contact Form 7
        add_action('wpcf7_mail_sent', [$this, 'handle_cf7']);

        // WPForms
        add_action('wpforms_process_complete', [$this, 'handle_wpforms'], 10, 4);

        // Gravity Forms
        add_action('gform_after_submission', [$this, 'handle_gravity_forms'], 10, 2);

        // WooCommerce New Order
        add_action('woocommerce_new_order', [$this, 'handle_woocommerce_order'], 10, 2);
    }

    /**
     * Contact Form 7 handler.
     */
    public function handle_cf7($contact_form): void {
        if (!get_option('moshtariat_crm_cf7_enabled', 1)) {
            return;
        }

        $submission = WPCF7_Submission::get_instance();
        if (!$submission) {
            return;
        }

        $posted_data = $submission->get_posted_data();
        $title = $contact_form->title();

        // Extract phone & name intelligently
        $name  = $posted_data['your-name'] ?? $posted_data['name'] ?? $posted_data['full_name'] ?? '';
        $phone = $posted_data['your-tel'] ?? $posted_data['your-phone'] ?? $posted_data['phone'] ?? $posted_data['tel'] ?? $posted_data['mobile'] ?? '';
        $email = $posted_data['your-email'] ?? $posted_data['email'] ?? '';
        $msg   = $posted_data['your-message'] ?? $posted_data['message'] ?? '';

        $this->api->send_lead([
            'title'        => "فرم سایت ({$title}): " . ($name ?: 'مخاطب وب‌سایت'),
            'contact_name' => $name,
            'phone'        => $phone,
            'email'        => $email,
            'description'  => $msg,
            'source'       => 'Contact Form 7',
            'tags'         => ['وردپرس', 'CF7', $title],
        ]);
    }

    /**
     * WPForms handler.
     */
    public function handle_wpforms($fields, $entry, $form_data, $entry_id): void {
        if (!get_option('moshtariat_crm_wpforms_enabled', 1)) {
            return;
        }

        $name = '';
        $phone = '';
        $email = '';
        $desc = [];

        foreach ($fields as $field) {
            $type = $field['type'] ?? '';
            $val  = $field['value'] ?? '';

            if ($type === 'name' || stripos($field['name'] ?? '', 'نام') !== false) {
                $name = $val;
            } elseif ($type === 'phone' || stripos($field['name'] ?? '', 'تلفن') !== false || stripos($field['name'] ?? '', 'تماس') !== false) {
                $phone = $val;
            } elseif ($type === 'email') {
                $email = $val;
            } else {
                $desc[] = ($field['name'] ?? '') . ': ' . $val;
            }
        }

        $form_title = $form_data['settings']['form_title'] ?? 'WPForms';

        $this->api->send_lead([
            'title'        => "فرم WPForms ({$form_title}): " . ($name ?: 'مخاطب وب‌سایت'),
            'contact_name' => $name,
            'phone'        => $phone,
            'email'        => $email,
            'description'  => implode("\n", $desc),
            'source'       => 'WPForms',
            'tags'         => ['وردپرس', 'WPForms', $form_title],
        ]);
    }

    /**
     * Gravity Forms handler.
     */
    public function handle_gravity_forms($entry, $form): void {
        if (!get_option('moshtariat_crm_gform_enabled', 1)) {
            return;
        }

        $name = '';
        $phone = '';
        $email = '';
        $desc = [];

        foreach ($form['fields'] as $field) {
            $val = rgar($entry, (string) $field->id);
            if (empty($val)) continue;

            $label = $field->get_field_label(false, '');
            if ($field->type === 'phone' || stripos($label, 'تلفن') !== false || stripos($label, 'موبایل') !== false) {
                $phone = $val;
            } elseif ($field->type === 'name' || stripos($label, 'نام') !== false) {
                $name = is_array($val) ? implode(' ', $val) : $val;
            } elseif ($field->type === 'email') {
                $email = $val;
            } else {
                $desc[] = $label . ': ' . (is_array($val) ? implode(', ', $val) : $val);
            }
        }

        $this->api->send_lead([
            'title'        => "فرم گرویتی ({$form['title']}): " . ($name ?: 'مخاطب وب‌سایت'),
            'contact_name' => $name,
            'phone'        => $phone,
            'email'        => $email,
            'description'  => implode("\n", $desc),
            'source'       => 'Gravity Forms',
            'tags'         => ['وردپرس', 'گرویتی_فرم', $form['title']],
        ]);
    }

    /**
     * WooCommerce order handler.
     */
    public function handle_woocommerce_order($order_id, $order = null): void {
        if (!get_option('moshtariat_crm_woo_enabled', 1)) {
            return;
        }

        if (!$order && function_exists('wc_get_order')) {
            $order = wc_get_order($order_id);
        }
        if (!$order) {
            return;
        }

        $name = trim($order->get_billing_first_name() . ' ' . $order->get_billing_last_name());
        $phone = $order->get_billing_phone();
        $email = $order->get_billing_email();
        $total = $order->get_total();
        $items = [];

        foreach ($order->get_items() as $item) {
            $items[] = $item->get_name() . ' × ' . $item->get_quantity();
        }

        $this->api->send_lead([
            'title'        => "سفارش ووکامرس #{$order_id}: {$name} (" . number_format((float)$total) . " تومان)",
            'contact_name' => $name,
            'phone'        => $phone,
            'email'        => $email,
            'description'  => "محصولات:\n" . implode("\n", $items) . "\n\nشهر: " . $order->get_billing_city(),
            'source'       => 'WooCommerce',
            'tags'         => ['ووکامرس', 'سفارش_جدید', 'فروشگاه'],
        ]);
    }
}
