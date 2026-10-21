<?php
/**
 * Moshtariat CRM Admin Settings Page.
 */

defined('ABSPATH') || exit;

class Moshtariat_CRM_Admin {

    private Moshtariat_CRM_API $api;

    public function __construct(Moshtariat_CRM_API $api) {
        $this->api = $api;
    }

    public function init(): void {
        add_action('admin_menu', [$this, 'add_menu']);
        add_action('admin_init', [$this, 'register_settings']);
        add_action('admin_post_moshtariat_test_connection', [$this, 'handle_test_connection']);
    }

    public function add_menu(): void {
        add_menu_page(
            'سی ار ام مشتریات',
            'مشتریات CRM',
            'manage_options',
            'moshtariat-crm',
            [$this, 'render_page'],
            'dashicons-chart-pie',
            30
        );
    }

    public function register_settings(): void {
        register_setting('moshtariat_crm_options', 'moshtariat_crm_enabled', ['sanitize_callback' => 'intval']);
        register_setting('moshtariat_crm_options', 'moshtariat_crm_endpoint', ['sanitize_callback' => 'esc_url_raw']);
        register_setting('moshtariat_crm_options', 'moshtariat_crm_token', ['sanitize_callback' => 'sanitize_text_field']);
        register_setting('moshtariat_crm_options', 'moshtariat_crm_cf7_enabled', ['sanitize_callback' => 'intval']);
        register_setting('moshtariat_crm_options', 'moshtariat_crm_wpforms_enabled', ['sanitize_callback' => 'intval']);
        register_setting('moshtariat_crm_options', 'moshtariat_crm_gform_enabled', ['sanitize_callback' => 'intval']);
        register_setting('moshtariat_crm_options', 'moshtariat_crm_woo_enabled', ['sanitize_callback' => 'intval']);
    }

    public function handle_test_connection(): void {
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }
        check_admin_referer('moshtariat_test_action', 'moshtariat_test_nonce');

        $res = $this->api->send_lead([
            'title'        => 'تست اتصال وردپرس به سی ار ام مشتریات',
            'contact_name' => 'مدیر سایت (' . wp_get_current_user()->display_name . ')',
            'phone'        => '09120000000',
            'email'        => get_option('admin_email'),
            'description'  => 'این یک درخواست آزمایشی برای بررسی سلامت اتصال وردپرس به پایپ‌لاین فروش مشتریات است.',
            'source'       => 'تست افزونه وردپرس',
            'tags'         => ['تست', 'وردپرس'],
        ]);

        $redirect = add_query_arg([
            'page'        => 'moshtariat-crm',
            'test_status' => $res['success'] ? 'success' : 'failed',
        ], admin_url('admin.php'));

        wp_safe_redirect($redirect);
        exit;
    }

    public function render_page(): void {
        if (!current_user_can('manage_options')) return;

        $logs = get_option('moshtariat_crm_recent_logs', []);
        $test_status = sanitize_text_field($_GET['test_status'] ?? '');
        ?>
        <div class="wrap" style="direction:rtl;text-align:right;max-width:900px;margin-top:20px">
            <h1 style="display:flex;align-items:center;gap:10px">
                <span class="dashicons dashicons-chart-pie" style="font-size:28px;width:28px;height:28px;color:#4F46E5"></span>
                تنظیمات اتصال به سی ار ام مشتریات (Moshtariat CRM)
            </h1>
            <p style="color:#64748B">
                با این افزونه، تمام درخواست‌های ثبت‌شده در فرم‌های تماس و سفارشات فروشگاه به‌صورت خودکار و بلادرنگ در بورد کانبان مشتریات ثبت می‌شوند.
            </p>

            <?php if ($test_status === 'success'): ?>
                <div class="notice notice-success is-dismissible" style="padding:10px 14px;margin:15px 0">
                    <p><b>اتصال با موفقیت برقرار شد!</b> یک سرنخ تستی در بورد کانبان سی ار ام مشتریات ثبت گردید.</p>
                </div>
            <?php elseif ($test_status === 'failed'): ?>
                <div class="notice notice-error is-dismissible" style="padding:10px 14px;margin:15px 0">
                    <p><b>خطا در برقراری ارتباط!</b> لطفاً نشانی وب‌هوک و توکن را بررسی کنید.</p>
                </div>
            <?php endif; ?>

            <div style="background:#fff;padding:24px;border:1px solid #E2E8F0;border-radius:12px;margin-top:20px;box-shadow:0 2px 10px rgba(0,0,0,0.03)">
                <form method="post" action="options.php">
                    <?php settings_fields('moshtariat_crm_options'); ?>

                    <table class="form-table" role="presentation">
                        <tr>
                            <th scope="row">وضعیت افزونه</th>
                            <td>
                                <label>
                                    <input type="checkbox" name="moshtariat_crm_enabled" value="1" <?php checked(get_option('moshtariat_crm_enabled', 1), 1); ?>>
                                    فعال‌سازی ارسال خودکار سرنخ‌ها به مشتریات
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">آدرس وب‌هوک / سرور مشتریات</th>
                            <td>
                                <input type="url" name="moshtariat_crm_endpoint" value="<?php echo esc_attr(get_option('moshtariat_crm_endpoint', 'https://moshtariat.ir/?controller=api&action=lead_ingress')); ?>" class="regular-text" style="direction:ltr;text-align:left">
                                <p class="description">نشانی اختصاصی درگاه ورودی لید (پیش‌فرض: https://moshtariat.ir/?controller=api&action=lead_ingress)</p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">توکن امنیتی (Bearer Token)</th>
                            <td>
                                <input type="password" name="moshtariat_crm_token" value="<?php echo esc_attr(get_option('moshtariat_crm_token', '')); ?>" class="regular-text" style="direction:ltr;text-align:left">
                                <p class="description">در صورتی که برای فضای کاری خود توکن امنیتی فعال کرده‌اید، آن را اینجا وارد کنید.</p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">اتصال به فرم‌سازها و فروشگاه</th>
                            <td>
                                <fieldset style="display:flex;flex-direction:column;gap:8px">
                                    <label>
                                        <input type="checkbox" name="moshtariat_crm_cf7_enabled" value="1" <?php checked(get_option('moshtariat_crm_cf7_enabled', 1), 1); ?>>
                                        ارسال پیام‌های <b>Contact Form 7</b>
                                    </label>
                                    <label>
                                        <input type="checkbox" name="moshtariat_crm_wpforms_enabled" value="1" <?php checked(get_option('moshtariat_crm_wpforms_enabled', 1), 1); ?>>
                                        ارسال پیام‌های <b>WPForms</b>
                                    </label>
                                    <label>
                                        <input type="checkbox" name="moshtariat_crm_gform_enabled" value="1" <?php checked(get_option('moshtariat_crm_gform_enabled', 1), 1); ?>>
                                        ارسال پیام‌های <b>Gravity Forms</b>
                                    </label>
                                    <label>
                                        <input type="checkbox" name="moshtariat_crm_woo_enabled" value="1" <?php checked(get_option('moshtariat_crm_woo_enabled', 1), 1); ?>>
                                        ارسال سفارشات جدید <b>ووکامرس (WooCommerce)</b> به عنوان لید
                                    </label>
                                </fieldset>
                            </td>
                        </tr>
                    </table>

                    <?php submit_button('ذخیره تغییرات'); ?>
                </form>

                <hr style="margin:24px 0;border:0;border-top:1px solid #E2E8F0">

                <div style="display:flex;justify-content:space-between;align-items:center">
                    <div>
                        <h3 style="margin:0 0 4px">تست زنده اتصال به CRM</h3>
                        <p style="margin:0;color:#64748B;font-size:13px">یک سرنخ آزمایشی به بورد فروش ارسال کنید تا صحت کارکرد تایید شود.</p>
                    </div>
                    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                        <input type="hidden" name="action" value="moshtariat_test_connection">
                        <?php wp_nonce_field('moshtariat_test_action', 'moshtariat_test_nonce'); ?>
                        <button type="submit" class="button button-secondary">
                            ارسال لید تستی به مشتریات
                        </button>
                    </form>
                </div>
            </div>

            <!-- Recent Logs -->
            <div style="background:#fff;padding:24px;border:1px solid #E2E8F0;border-radius:12px;margin-top:20px">
                <h3 style="margin-top:0">آخرین لاگ‌های ارسالی به مشتریات</h3>
                <?php if (empty($logs)): ?>
                    <p style="color:#64748B">هنوز درخواستی ارسال نشده است.</p>
                <?php else: ?>
                    <table class="widefat striped" style="direction:rtl">
                        <thead>
                            <tr>
                                <th>زمان</th>
                                <th>عنوان سرنخ</th>
                                <th>شماره تماس</th>
                                <th>منبع</th>
                                <th>وضعیت</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($logs as $log): ?>
                                <tr>
                                    <td><?php echo esc_html($log['time']); ?></td>
                                    <td><?php echo esc_html($log['title']); ?></td>
                                    <td style="direction:ltr;text-align:right"><?php echo esc_html($log['phone']); ?></td>
                                    <td><span class="badge" style="background:#EEF2FF;color:#4F46E5;padding:2px 8px;border-radius:4px"><?php echo esc_html($log['source']); ?></span></td>
                                    <td>
                                        <?php if (!empty($log['success'])): ?>
                                            <span style="color:#10B981;font-weight:bold">موفق (کد <?php echo esc_html($log['status']); ?>)</span>
                                        <?php else: ?>
                                            <span style="color:#EF4444;font-weight:bold">خطا (کد <?php echo esc_html($log['status']); ?>)</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>

            <!-- Branding footer -->
            <div style="margin-top:24px;text-align:center;color:#64748B;font-size:13px">
                طراحی و توسعه‌یافته توسط تیم <a href="https://moshtariat.ir" target="_blank" style="color:#4F46E5;font-weight:bold">سی ار ام مشتریات</a> · 
                <a href="https://moshtariat.ir/what-is-crm" target="_blank">راهنمای CRM</a> · 
                <a href="https://moshtariat.ir/pricing" target="_blank">پلن رایگان ۵۰ لید</a>
            </div>
        </div>
        <?php
    }
}
