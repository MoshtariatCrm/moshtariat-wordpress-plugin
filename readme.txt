=== Moshtariat CRM Integration ===
Contributors: moshtariat, matinwd
Donate link: https://moshtariat.ir
Tags: crm, lead-management, sales-pipeline, contact-form-7, woocommerce
Requires at least: 5.6
Tested up to: 6.7
Stable tag: 1.0.0
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Connect WordPress forms (Contact Form 7, WPForms, Gravity Forms) and WooCommerce orders directly to Moshtariat CRM agile pipeline.

== Description ==

**Moshtariat CRM Integration (اتصال وردپرس به سی ار ام مشتریات)** is the official WordPress plugin for [Moshtariat CRM](https://moshtariat.ir) — the modern, lightweight, and agile B2B CRM designed for growing businesses, sales teams, and online shops in Iran.

Instead of losing leads in form emails or manually copying customer numbers into Excel, this plugin automatically captures every lead and sends it directly to your designated Kanban column in Moshtariat CRM in real time.

### 🚀 Key Features

* **Instant Lead Ingress:** Automatically transfers form entries to your Moshtariat sales pipeline within seconds.
* **Contact Form 7 Support:** Seamless out-of-the-box integration with CF7 without any complex configuration.
* **WPForms & Gravity Forms:** Full support for custom field mapping (Name, Phone, Email, Notes).
* **WooCommerce Orders as Leads:** Turns new e-commerce orders into follow-up cards for your sales team.
* **Golden Hour Follow-up:** Enables your team to respond to leads while they are hot.
* **Activity Logs:** Review the last 10 webhook delivery attempts with HTTP response codes directly in your WordPress admin.
* **Zero Overhead:** Built with native WordPress HTTP APIs (`wp_remote_post`), lightweight and clean.

### 🌐 Official Resources

* **Official Website:** [https://moshtariat.ir](https://moshtariat.ir)
* **What is CRM Guide (راهنمای جامع CRM چیست):** [https://moshtariat.ir/what-is-crm](https://moshtariat.ir/what-is-crm)
* **Free Tier (۵۰ لید رایگان دائمی):** [https://moshtariat.ir/pricing](https://moshtariat.ir/pricing)
* **Features & Kanban Pipeline:** [https://moshtariat.ir/features](https://moshtariat.ir/features)
* **Developer SDK:** [https://github.com/MoshtariatCrm/moshtariat-api-client](https://github.com/MoshtariatCrm/moshtariat-api-client)

== Installation ==

1. Upload the `moshtariat-crm` folder to the `/wp-content/plugins/` directory, or install the plugin directly through the WordPress plugins screen.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Navigate to **مشتریات CRM** in your admin menu.
4. Enter your Moshtariat CRM webhook endpoint and optional token.
5. Click **ارسال لید تستی به مشتریات** to verify the connection.

== Frequently Asked Questions ==

= آیا استفاده از این افزونه رایگان است؟ =
بله، این افزونه کاملاً رایگان و متن‌باز (GPLv2) است. همچنین پلتفرم سی ار ام مشتریات دارای پلن رایگان دائمی تا ۵۰ لید هدیه بدون نیاز به کارت بانکی می‌باشد.

= چه فرم‌سازهایی پشتیبانی می‌شوند؟ =
افزونه به صورت خودکار از فرم‌سازهای پرطرفدار از جمله Contact Form 7، WPForms، Gravity Forms و همچنین سفارشات ووکامرس پشتیبانی می‌کند.

= چگونه می‌توانم وضعیت ارسال لیدها را بررسی کنم؟ =
در صفحه تنظیمات افزونه، جدولی از ۱۰ ارسال اخیر به همراه زمان دقیق، شماره تماس و کد وضعیت وب‌سرویس نمایش داده می‌شود.

== Screenshots ==

1. صفحه تنظیمات افزونه در پیشخوان وردپرس و تست اتصال با یک کلیک.
2. جدول لاگ‌های ارسال لید به سی ار ام مشتریات.

== Changelog ==

= 1.0.0 =
* Initial public release on WordPress.org.
* Added native support for Contact Form 7, WPForms, Gravity Forms, and WooCommerce.
* Added one-click live connection testing and delivery logging.
