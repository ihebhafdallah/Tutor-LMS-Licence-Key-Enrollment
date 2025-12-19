<p align="center">
  <picture>
    <source media="(prefers-color-scheme: dark)" srcset="https://tutorlms.com/wp-content/uploads/2024/04/brand-assets-tutor-logo-light.webp">
    <source media="(prefers-color-scheme: light)" srcset="https://tutorlms.com/wp-content/uploads/2024/04/brand-assets-tutor-logo-dark.webp">
  </picture>
</p>



# Tutor LMS Licence Key Enrollment
Tutor LMS Licence Key Enrollment is a WordPress plugin that enhances Tutor LMS by enabling course enrollment through unique license keys. Administrators can generate bulk license keys for specific courses and distribute them to users. Users can then use a simple frontend form to submit their key and gain instant access to the course.

This plugin is ideal for offline sales, corporate training, academic institutions, or any scenario where you need to grant course access without a standard online payment process.

## Features

#### Administrator Features
*   **Bulk Key Generation**: Generate multiple license keys (5, 10, 25, 50, 100 at a time) for any Tutor LMS course.
*   **Key Management Interface**: A dedicated "Licence Keys" menu under Tutor LMS to view and manage all generated keys.
*   **Key Status Tracking**: Keys have a status (`active`, `sent`, `expired`) to help you track their lifecycle.
*   **Filtering & Sorting**: Easily filter keys by course and status, or sort them by creation date.
*   **Bulk Actions**: Update the status of multiple keys at once (e.g., Mark as Sent, Mark as Expired) or delete them in bulk.
*   **CSV Export**: Export all keys or a filtered selection to a CSV file for external record-keeping or distribution.

#### User Features
*   **Simple Enrollment Form**: A clean and straightforward form for users to enter their license key.
*   **Instant Access**: Upon submitting a valid key, users are automatically enrolled in the corresponding course.
*   **User-Friendly Notifications**: Clear success or error messages guide the user through the activation process.

## Requirements
*   WordPress
*   [Tutor LMS](https://wordpress.org/plugins/tutor/)
*   [WooCommerce](https://wordpress.org/plugins/woocommerce/) (The plugin uses WooCommerce to process the enrollment and create a zero-cost order for record-keeping).

## Installation

1.  Download the latest release as a `.zip` file from this repository.
2.  In your WordPress Admin Dashboard, navigate to **Plugins > Add New**.
3.  Click the **Upload Plugin** button at the top of the page.
4.  Select the `.zip` file you downloaded and click **Install Now**.
5.  Once installed, click **Activate Plugin**.

Alternatively, you can manually install the plugin:
1.  Unzip the downloaded file.
2.  Upload the `tutor-lms-licence-key-enrollment` folder to your `wp-content/plugins/` directory.
3.  Navigate to the **Plugins** page in your WordPress dashboard and activate the plugin.

## Usage

#### 1. Generating License Keys (Admin)

1.  From your WordPress dashboard, navigate to **Tutor LMS > Licence Keys**.
2.  In the "Generate Licence Keys" section, select a course from the dropdown menu.
3.  Choose the number of keys you wish to generate.
4.  Click the **Generate Keys** button.
5.  The newly created keys will appear in the "Licence Keys" list below. You can now copy and distribute these keys to your users.

#### 2. Enrolling with a License Key (User)

1.  Create a new page or edit an existing one where you want users to enroll (e.g., a page named "Activate Your Course").
2.  Add the following shortcode to the page's content:
    ```
    [tutor_licence_key]
    ```
3.  Publish or update the page.
4.  Direct your users to this page. They must be logged into their account to see the form.
5.  The user enters their provided license key into the form and clicks "Activate Licence".
6.  If the key is valid, they will be enrolled in the course and see a success message. The key's status will automatically be updated to `expired`.

## Internationalization

This plugin is ready for translation and includes the following language packs:
*   **Arabic** (ar)
*   **French** (fr_FR)