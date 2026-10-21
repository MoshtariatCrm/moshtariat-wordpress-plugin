<?php
/**
 * Moshtariat API Dispatcher.
 */

defined('ABSPATH') || exit;

class Moshtariat_CRM_API {

    private string $default_endpoint = 'https://moshtariat.ir/?controller=api&action=lead_ingress';

    public function get_endpoint(): string {
        $endpoint = get_option('moshtariat_crm_endpoint', $this->default_endpoint);
        return !empty($endpoint) ? esc_url_raw($endpoint) : $this->default_endpoint;
    }

    public function get_token(): string {
        return sanitize_text_field(get_option('moshtariat_crm_token', ''));
    }

    public function is_enabled(): bool {
        return (bool) get_option('moshtariat_crm_enabled', 1);
    }

    /**
     * Send lead payload to Moshtariat CRM.
     */
    public function send_lead(array $lead_data): array {
        if (!$this->is_enabled()) {
            return ['success' => false, 'error' => 'Plugin is disabled in settings.'];
        }

        $endpoint = $this->get_endpoint();
        $token    = $this->get_token();

        $body = wp_json_encode([
            'title'        => sanitize_text_field($lead_data['title'] ?? 'سرنخ جدید وب‌سایت'),
            'contact_name' => sanitize_text_field($lead_data['contact_name'] ?? ''),
            'phone'        => sanitize_text_field($lead_data['phone'] ?? ''),
            'email'        => sanitize_email($lead_data['email'] ?? ''),
            'description'  => sanitize_textarea_field($lead_data['description'] ?? ''),
            'source'       => sanitize_text_field($lead_data['source'] ?? 'WordPress'),
            'tags'         => array_map('sanitize_text_field', (array) ($lead_data['tags'] ?? ['وب‌سایت'])),
            'meta'         => $lead_data['meta'] ?? [],
        ]);

        $headers = [
            'Content-Type' => 'application/json; charset=utf-8',
            'User-Agent'   => 'MoshtariatWordPress/' . MOSHTARIAT_CRM_VERSION . '; ' . home_url(),
        ];

        if (!empty($token)) {
            $headers['Authorization'] = 'Bearer ' . $token;
        }

        $response = wp_remote_post($endpoint, [
            'method'      => 'POST',
            'timeout'     => 15,
            'redirection' => 5,
            'httpversion' => '1.1',
            'blocking'    => true,
            'headers'     => $headers,
            'body'        => $body,
        ]);

        $status = wp_remote_retrieve_response_code($response);
        $res_body = wp_remote_retrieve_body($response);

        $log_entry = [
            'time'    => current_time('mysql'),
            'title'   => $lead_data['title'] ?? '',
            'phone'   => $lead_data['phone'] ?? '',
            'source'  => $lead_data['source'] ?? 'WordPress',
            'status'  => $status,
            'success' => ($status >= 200 && $status < 300),
        ];
        $this->save_log($log_entry);

        if (is_wp_error($response)) {
            return ['success' => false, 'error' => $response->get_error_message()];
        }

        $decoded = json_decode($res_body, true);
        return [
            'success' => ($status >= 200 && $status < 300),
            'status'  => $status,
            'data'    => $decoded ?: $res_body,
        ];
    }

    private function save_log(array $entry): void {
        $logs = get_option('moshtariat_crm_recent_logs', []);
        if (!is_array($logs)) {
            $logs = [];
        }
        array_unshift($logs, $entry);
        $logs = array_slice($logs, 0, 10);
        update_option('moshtariat_crm_recent_logs', $logs, false);
    }
}
