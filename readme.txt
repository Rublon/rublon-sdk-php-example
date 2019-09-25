How to use PHP SDK Example?

1. Copy directory of PHP SDK Example to your public folder (e.g. XAMPP, WAMP Server, AMPPS etc.).
2. Add new application in Rublon Admin Console (with type "Custom integration using PHP SDK").
3. Configure config.php file by modify all properties. You can fetch RUBLON_SYSTEM_TOKEN and RUBLON_SECRET_KEY from Admin Console. RUBLON_API_SERVER is URI to the Rublon CORE.
USER_PASSWORD property is used only to validate a login form.
4. Run `composer install` command in a root directory of this example project. [Composer must be installed]
5. Open a site in your browser.
6. When PHP SDK Example has been correctly loaded, you can switch login form from 2factor method to the Passwordless login method and back.