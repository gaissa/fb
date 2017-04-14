==== WooCommerce Easy Booking ====
Contributors: @_Ashanna, @amsul (For pickadate.js)
Tags: woocommerce, booking, renting, products, book, rent, e-commerce
Requires at least: 4.0, WooCommerce 2.5
Tested up to: 4.7.3, WooCommerce 3.0
Stable tag: 2.1.1
License: GPLv2 or later

WooCommerce Easy Booking allows to easily book or rent WooCommerce products.

== Description ==

This plugins adds an option to WooCommerce products in order to book or rent them.

It adds one or two fields to the product page : a start date and maybe an end date and, if necessary, calculates a new price based on a daily, nightly, weekly or custom basis (E.g. $4 per day / night / week / custom period).

It uses [Pickadate.js](http://amsul.ca/pickadate.js/ "Pickadate.js") to display the calendars and set the dates.

See the plugin demo [here](http://demo.herownsweetcode.com/ "Easy Booking demo").

For more features, check these add-ons:

* [Easy Booking: Availability Check](http://herownsweetcode.com/easy-booking/plugin/availability-check/ "Easy Booking: Availability Check") to manage stocks and availabilities.
* [Easy Booking: Duration Discounts](http://herownsweetcode.com/easy-booking/plugin/duration-discounts/ "Easy Booking: Duration Discounts") to apply discounts or surcharges depending on booking duration.
* [Easy Booking: Disable Dates](http://herownsweetcode.com/easy-booking/plugin/disable-dates/ "Easy Booking: Disable Dates") to disable days, dates or dateranges for booking.
* [Easy Booking: Pricing](http://herownsweetcode.com/easy-booking/plugin/pricing/ "Easy Booking: Pricing") to set different prices depending on days, dates or dateranges.

== Installation ==

First, you need to install and activate WooCommerce.

1. Install the "WooCommerce Easy Booking" Plugin.
2. Activate the plugin.
3. In your administration panel, go to the "Easy Booking" menu to set up the plugin.
4. In your administration panel, go to the WooCommerce product page your want to allow for booking or renting.
5. Check the "Bookable" checkbox next to the product type dropdown.

* For variable products, you must check the "Bookable" option both on parent product and on the variation you want to set as bookable. You can have bookable and non-bookable variation for the same product, but if one of the variation is bookable, the parent product must be set as bookable too.

* For grouped products, you must set the parent AND children products as bookable. Otherwise, it won't work correctly,

6. If you want to set booking options for each product, go to the "Bookings" tab on the product page (or the "Variations" tab for variable products to manage booking options at variation level). You can also set these options for every products in the plugin settings.

7. And that's it!

== Frequently Asked Questions ==

= The calendar doesn’t work. =

If the calendar doesn’t show up, there might be a Javascript error. Open your navigator’s console (F12 > Console), and look for any error. Translation files might be missing, use the contact form or the support forum if it is the case.

If you are on a multisite and the calendar is not working, make sure to save the plugin settings again to generate the stylesheet corresponding to your site.

= How can I remove the price calculation? =

You can use the easy_booking_two_dates_price filter to return the base product price instead of the new calculated price.
Please refer to the plugin [documentation](http://herownsweetcode.com/easy-booking/documentation/easy-booking/faq/) for more information.

= Is WooCommerce Easy Booking compatible with Networked Sites? =

Yes, version 1.5 is now compatible with multisites. You will need to install the plugin on the network, and then activate it on each site.

For the addons, you need to enter the license keys on the network.

= I can't make a product not bookable. =

Make sure to uncheck the “Make all products bookable?” options on the plugin settings page (Easy Booking > Settings) if you want to have not bookable products.

= How to make a product bookable and not bookable at the same time? =

You can make a variable product with two variations : one bookable and one as a regular product.

= Where are the "Start text", "End text" and "Information text" options? =

These options were removed from the plugin in version 2.0, because it was impossible to make these custom texts translation-ready.

= So, how can I change the "Start" and "End" texts? =

See this code: https://gist.github.com/Ashanna/94da0e88dd6e1a498632feee3f67b903 and add it to your theme's functions.php file.

=  And the information text? =

To add an information text before the datepicker(s), see the code: https://gist.github.com/Ashanna/0e5594c04eef1a2cd9f0beb9f5036904 and add it to your theme's functions.php file.

= How can I change the " / day" and "Select dates" texts? =

There are two filters available. 'easy_booking_display_price' and 'woocommerce_loop_add_to_cart_link'.
Please refer to the plugin [documentation](http://herownsweetcode.com/easy-booking/documentation/easy-booking/faq/) for more information.

= Can I have hours instead or in additon to dates? =

For the moment this is not possible.

= Can the dates be selected on the order page and applied to all products in the cart? =

No, the dates need to be selected on the product page for each product individually.

= The automatic update doesn't work for the addons =

If you have any issue with the updates of the addons, please [send a message](http://herownsweetcode.com/#contact) with your order number and license key to get the latest version.

= How can I help the developpement of this plugin? =

Developping and maintaining WordPress plugins take a lot of time, if you want to support the development you can get one or several addons for Easy Booking [here](http://herownsweetcode.com/easy-booking/). Thank you!

== Screenshots ==

1. Product page
2. Calendar
3. Selected dates

== Changelog ==

= 2.1.1 =

* Fix - Compatibility with WooCommerce 3.0 and WooCommerce Product Bundles 5.2.0.
* Fix - Fixed an issue with one date selection and product ID not sent in the ajax request with WooCommerce 3.0.
* Fix - Fixed an issue with variable products with no available variation.

= 2.1.0 =

* Fix - Compatibility with WooCommerce > 3.0
* Fix - Removed "name=variation_id" to avoid conflicts.

= 2.0.9 =

* Updated addons page.
* Fix - Return price if 0 after selecting dates.
* Fix - Compatibility with WooCommerce Deposits.
* Fix - Compatibility with WooCommerce Product Addons and variable products.

= 2.0.8 =

* Fix - Compatibility with WooCommerce Product Bundles > 5.0. If you use it, you must have at least version 5.0.
* Fix - Compatibility with the future Easy Booking: Pricing extension.
* Fix - [Localization] Added "Close" translation in catalan and added ca.js file.
* Fix - [Localization] Updated wceb.pot file.
* Removed - [Localization] Removed FR and NL included language files. To download language files, please visit http://herownsweetcode.com/easy-booking/documentation/easy-booking/localization/.

= 2.0.7 =

* Fix - Fixed wceb_get_product_price function and compatiblity with Membership plugins.
* Fix - [Frontend] Total booking duration displaying "x day(s)" in "nights" mode, instead of "x night(s)".
* Fix - WooCommerce Product Bundles compatibility.

= 2.0.6 =

* Add - [Filter] 'easy_booking_multiply_additional_costs' filter to multiply or not additional costs by booking duration (default: false).
* Add - [Filter] 'easy_booking_product_booking_min' to override product settings.
* Add - [Filter] 'easy_booking_product_booking_max' to override product settings.
* Add - [Filter] 'easy_booking_product_first_available_date' to override product settings.
* Add - [Fitler] 'easy_booking_display_average_price' to display or not the average price / day after selecting the dates (default: false).
* Add - [Filter] 'easy_booking_booking_price_details' to override booking price details (total booking duration and (maybe) average price / day).
* Add - [Filter] 'easy_booking_one_date_price' returns booking price for one date selection.
* Add - [Filter] 'easy_booking_two_dates_price' returns booking price for two dates selection.
* Add - [Frontend] Display regular and sale booking prices if the product is on sale.
* Add - [Frontend] Display the total booking duration after selecting the dates.
* Removed - [Filter] 'easy_booking_get_new_item_price' (replaced by 'easy_booking_one_date_price' and 'easy_booking_two_dates_price').
* Removed - [Filter] 'easy_booking_get_new_grouped_item_price' (replaced by 'easy_booking_one_date_price' and 'easy_booking_two_dates_price').
* Fix - [Frontend] Issue with cache plugins and nonce where the booking price wasn't updated correctly.

= 2.0.5 =

* Fix - [Frontend] Issue with bookable and non-bookable variations with the add to cart button being disabled.

= 2.0.4 =

* Fix - [Admin] Dates are now correctly saved when modifying and saving an order on the admin.

= 2.0.3 =

* Fix - [Security] Security fixes on ajax requests.
* Fix - [Frontend] Set customer session only if necessary.
* Fix - [Admin] Issue when changing dates in the admin order page.

= 2.0.2 =

* Fix - [Frontend] Issue with first available date.
* Fix - [Frontend] Issue with WooCommerce Product Addons with several addons per product.
* Fix - [Admin] Issue when saving appearance settings and "Make all products bookable?" option.

= 2.0.1 =

* Fix   - [Admin] Issue on the admin order page when order item is not a product.
* Fix   - [Frontend] Set the pickers highlight and view to the first available date.
* Fix   - [Admin] Issue with character encoding in pickadate.js.
* Tweak - [Filters] Deleted 'easy_booking_currency' and 'easy_booking_new_price_args' filters when returning the price.

= 2.0.0 =

/!\ Please update the addons if you have any and want to use this new version. Because of the new features added, it is not compatible with older versions.

Compatible versions of the addons:

- Availability Check > 1.5.
- Duration Discounts > 1.7.
- Disbale Dates > 1.5.

* Add   - [Feature] Possibility to select only one date.
* Add   - [Feature] Compatibility with WooCommerce Product Bundles.
* Add   - [Filter] 'easy_booking_start_text' filter to change the "Start" text and make it translation-ready.
* Add   - [Filter] 'easy_booking_end_text' filter to change the "End" text and make it translation-ready.
* Add   - [Filter] 'easy_booking_information_text' filter to change the information text and make it translation-ready.
* Fix   - [Frontend] Save the date to the right format when adding a product to the cart.
* Fix   - [Admin] Display datepickers when manually adding a bookable product to an order in the admin.
* Tweak - Enable "Add to cart" button only once the date(s) is (are) selected.
* Tweak - Changed ajax_object variable into wceb_object, to avoid conflicts with other plugins.

= 1.9.2 =

* Fix - [Admin] Issue with the "Make all products bookable?" option.
* Fix - [Frontend] Load scripts even if product price is 0.

= 1.9.1 =

* Fix - [Frontend] Error when setting maximum booking duration to 1.

= 1.9.0 =

This update contains major changes / improvements. Do not hesitate to go to the [support forum](http://herownsweetcode.com/support/woocommerce-easy-booking/) or to [send a message](http://herownsweetcode.com/contact/) if you see any issue, as it's hard to test everything.

/!\ You will also need to update the addons - if you have any - to these versions in order to have everything work:

* Availability Check 1.4
* Duration Discounts 1.6
* Disable Dates 1.4

Inferior versions of the addons will NOT work with WooCommerce Easy Booking 1.9.

Make sure to clear your navigator's cache if you experience any issue, so it loads the latest files.

* Add     - [Feature] Weekly or custom period bookings and pricing. It is now possible to define the booking duration (either for every product or individually) and the price will be calculated depending on this duration. E.g: weekly, 2 days, 4 nights, etc.
* Add     - [Frontend] Improved calendars CSS to prevent conflicts with themes.
* Add     - [Filter] 'easy_booking_allowed_product_types' filter to allow custom product types (by default only simple, variable and grouped products are allowed to be bookable).
* Add     - [Localization] Missing Swedish translation.
* Add     - [Admin] 'wceb_get_version' function to get the plugin's version.
* Removed - [Admin] 'manage_bookings' option on variable products.
* Removed - [Admin] Unnecessary functions on the order admin page.
* Removed - [Localization] Duplicate elements on Pickadate.js French translation.
* Fix     - [Frontend] Booking price showing when the variation is not bookable.
* Fix     - [Admin] Register frontend scripts before enqueue.
* Fix     - [Admin] [Frontend] Dependencies when loading scripts.
* Fix     - [Admin] Check date format before getting booked items from orders.
* Tweak   - [Admin] Dates are now managed in dateranges if possible, instead of individually, to prevent too heavy arrays in Javascript.
* Tweak   - [Admin] Escape reports fields and CSS settings.
* Tweak   - Reviewed, updated and improved code to optimize the plugin.

= 1.8.2 =

* Fix - [Admin] Issue when not being able to make products not bookable.

= 1.8.1 =

* Fix     - [Frontend] Allow price to be 0 when adding a bookable product to the cart.
* Tweak   - [Admin] Limit max year to current year + 10 years, to avoid having too much data to load for Disabled Dates.
* Removed - [Admin] Removed price calculation when modifying dates on the order page (price has to be updated manually now).

= 1.8 =

* Add   - [Filter] 'easy_booking_frontend_parameters' filter to pass extra parameters to the pickadate-custom.js file.
* Add   - [Disable Dates] Compatibility with the new feature of Easy Booking: Disable Dates, which allows to have disabled dates inside the booking period.
* Add   - [Settings] Option to set the first weekday to Monday or Sunday.
* Add   - [Admin] Function to load a template from the theme instead of the plugin, in case you want to override it. Overriden templates must be placed in a folder named 'easy-booking' (only works for 'wceb-html-product-view.php' - template for the datepicker inputs  - for the moment).
* Fix   - [Frontend] Issue when no date was disabled.
* Fix   - [Frontend] Wrong price calculation with WooCommerce Product Add-Ons and prices without taxes.
* Fix   - [Frontend] [Grouped products] Reset and render datepickers after changing each product quantity.
* Fix   - [Frontend] [Variable products] Selects "input[name=variation_id]"" instead of "input.variation_id" to get the selected variation ID to ensure maximum compatibility with themes.
* Fix   - [Admin] Booking meta boxes showing when they shouldn't.
* Fix   - [Frontend] [Variable products] Wrong price formatting.
* Fix   - [Admin] [Variable products] Booking fields were not showing correctly on variations.
* Fix   - [Admin] Escape settings correctly.
* Fix   - Compatibility with WooCommerce 2.5.0.
* Tweak - [Settings] Improved settings and addons pages and notices.
* Tweak - [Frontend] Format and sanitize correctly the information text.

= 1.7.6 =

* Add - Multisite support for addons and their license keys.
* Add - Uninstall file to clean database when deleting the plugin.
* Fix - Variable product prices not displaying when the product and variations are not bookable.
* Update - .pot file and French translation.

= 1.7.5 =

* Fix - Issue setting start date as "Array" instead of the date when booking a product.

= 1.7.4 =

* Fix - Wrong calculation of interval when weekdays are disabled (compatibility with Easy Booking : Disable Dates).

= 1.7.3 =

* Add - Option to set a year limit for bookings.
* Add - Compatibility with Easy Booking : Disable Dates.
* Add - Constant to define path and suffix to load scripts and styles.
* Add - wceb.pot file for translations.
* Fix - Price was updated when changing product quantity with WooCommerce Product Addons, even if there was no addon cost.
* Fix - Variation price was not updated after selecting dates.
* Fix - Display variation booking settings on the admin product page when changing the variation booking option.
* Update - French Translation.

= 1.7.2 =

* Add - Constant to load Pickadate.js translations.
* Fix - Load accounting.js for grouped products.

= 1.7.1 =

* Fix - Replaced a missing function on some PHP installations.

= 1.7 =

This update contains major changes / improvements. Be careful before updating and do not hesitate to go to the support forum or to send a message if you see any issue, as it's hard to maintain everything :)

* Add - Compatibility with grouped products.
* Add - Possibility to make all products bookable at once, and set booking options for all products.
* Fix - Wrong price calculation with prices excluding taxes.
* Fix - Variable products - Display booking price even if all variations have the same price.
* Fix - Calculated price with WooCommerce Product Add-ons.
* Fix - Prevent adding product to cart if there was an error before dates check (like a required WooCommerce Product Add-Ons field not set).
* Improved - Reports table.
* Improved - Notices.
* Improved - Better way to load minified scripts.

= 1.6.1 =

* Fix - CSS generation.
* Fix - disabled link on the add-ons page.

= 1.6 =

/!\ You might have to check variable products after this update. Backward compatibility should be ok, but you might have to check the "Bookable" checkbox again.

* Fix - Hook when saving plugin settings is now triggered when actually saving plugin settings.
* Fix - New way to generate and minify CSS. The old one was causing issues, especially with multisites.
* Fix - Calendars CSS, causing issues and conflicting with themes.
* Add - Possibility to manage booking at parent product level for variable products.
* Add - Add-ons page on the admin.
* Add - 'easy_booking_enqueue_additional_scripts' hook to enqueue scripts before the main pickadate script.
* Add - 'easy_booking_pickadate_dependecies' filter to add dependecies for the main pickadate script.
* Add - Custom Jquery events when initiliazing and setting calendars.
* Tweak - Improved Javascript for better flexibility and performance.

= 1.5.2 =

* Fix - Issue with WordPress 4.2.2 causing an error.

= 1.5.1 =

* Fix - Right to left function deprecated in WordPress 4.2.
* Fix - Backward compatibility with product booking metadata.
* Fix - First available date on start picker when minimum booking duration is set.
* Fix - is_bookable() function for variable products.
* Fix - Removed unnecessary Ajax call when clearing booking session.
* Fix - Input focus which made the calendar pop up when closing and opening window.
* Fix - Generated CSS after saving plugin settings.
* Fix - Registered CSS file for multisites.
* Fix - Price displayed on archive page for bookable products.
* Fix - Displayed price on non-bookable variable products.
* Add - Reports page on the admin.
* Add - "/ night" price when in "nights" mode.
* Add - Remove "/ day" or "/ night" text when variation is not bookable.
* Add - "WooCommerce Product Add-ons" compatibility. Please, refer to the documentation for more information about this : http://herownsweetcode.com/product/woocommerce-easy-booking/#documentation.
* Add - Automatically open second date picker after selecting first date.
* Add - Calendar titles.
* Add - Minifying CSS on-the-fly after saving plugin settings.
* Add - Close button on the calendar.
* Update - Pickadate.js version 3.5.6.
* Remove - WooCommerce Currency Switcher compatibility. Please, refer to the documentation to makes these plugins compatible : http://herownsweetcode.com/product/woocommerce-easy-booking/#documentation.

= 1.5 =

This update contains major changes for variable products. Backwards compatibility should be ok, but still check your variations after updating.

* Add - Variations are now handled individually, instead of inheriting from the parent product.
* Add - Multisite compatibility.
* Add - Right to left CSS, for right to left languages.
* Fix - Wrong price calculation when modifying an order.
* Fix - Security changes.
* Fix - Picker inputs pointer cursor.
* Fix - Added en.js file.
* Fix - Wrong $wpdb calls.
* Fix - Display product price on the right format.
* Tweak - Regenerate CSS only after saving plugin settings.
* Tweak - Improved Inputs CSS.
* Localization - Added Dutch translation.
* Localization - Update French translation.

= 1.4.4 =

* Fix - Javascript error on the notices

= 1.4.3 =

Easy Booking : Availability Check, the add-on to manage stocks and availabilities for WooCommerce Easy Booking is available !
Get it now on http://herownsweetcode.com/product/easy-booking-availability-check/ !

* Add - Admin notices styles.
* Fix - Removed WooCommerce loading gif (which was not loaded, causing Javascript errors).

= 1.4.2 =

* Fix - Issue with WooCommerce 2.3 and variable products.
* Fix - Issue with WooCommerce 2.3 and products.
* Fix - Issue with WooCommerce 2.3 on the order page.
* Fix - Issue when calculating new price and taxes on the order page.
* Fix - Removed minimum start date on the calendar on the product page.
* Add - Another theme for the calendar.
* Add - Hook when saving settings.
* Add - Filter when calculating new price.
* Add - Filter when calculating new price on the order page.
* Add - Filter for the displayed price on the product page.
* Add - Elements for the future Stock Management plugin.
* Removed - Spanish translation.
* Update - French translation.
* Update - Calendar CSS.
* Dev - Refactored code and plugin's structure.

= 1.4.1 =

* Fix - Fixed an error when updating orders.
* Fix - Fixed an error when adding a normal product to cart.
* Add - Spanish translation.
* Add - Display base price for one day on the product page.
* Add - Added an option to set the first available date.
* Update - French translation.

= 1.4 =

* Add - Option to set a minimum and a maximum booking duration for each product.
* Add - Possibility to change booking dates on the order page.
* Add - Possibility to add booking products on the order page.
* Add - en_GB translation file for the calendar.
* Add - WooCommerce Currency Switcher Compatibility
* Fix - Timezone issue with the datepicker.
* Fix - Prevent adding a product to the cart after clicking the "clear" button on the calendar.
* Fix - Incorrect selected dates with keyboard.
* Fix - Wrong price displayed when "Price excluding tax" is set on the product page.

= 1.3.1 =

* Fixed an issue where products were not added to cart if the user was not logged in.

= 1.3 =

#### This update has a lot of modifications, please do not hesitate to tell me if it's not working on the support forum here https://wordpress.org/support/plugin/woocommerce-easy-booking-system.

* Disabled dates before first date and dates after second dates, preventing users to select invalid dates
* Prevent users to select the same date in "nights" mode
* Fixed an error in the calculation price for one day in "days" mode
* Prevent product add to cart if one or both dates are missing
* Changed the way selected dates were set (old : post meta, new : session) so it doesn't affect the product itself
* Updated and cleaned Ajax requests
* Added a few things for the future stock management plugin
* Corrected an error in the French translation
* Added US translation for pickadate.js

= 1.2.2 =
* You can now choose whether to calculate the final price depending on number of days or number of nights.

= 1.2.1 =
* Changed the way CSS was added
* Security update

= 1.2 =
* The calendar is now fully customizable !
* Fixed an issue with variable products' sale price
* Added filters to easily change picker form
* Security updates
* Scripts updates
* Updated French translation

= 1.1 =
* Fixed a few issues
* WooCommerce EBS now works with variable products

= 1.0.5 =
* Fixed issues with WooCommerce 2.2

= 1.0.4 =
* Added price format
* Updated French translation

= 1.0.3 =
* Fixed an issue where fields were not showing up on product page

= 1.0.2 =
* Fix for WooCommerce 2.1

= 1.0.1 =
* Disabled dates before current day

= 1.0 =
* Initial Release

== Upgrade Notice ==

= 2.0.6 =

Only compatible with Easy Booking: Duration Discounts > 1.7.2.

= 2.0.0 =

Please update the addons if you have any and want to use this new version. Because of the new features added, it is not compatible with older versions.

= 1.9.0 =

This update contains major changes. Make backups and read the changelog before updating. Only compatible with the latest addons versions: Availability Check 1.4, Duration Discounts 1.6, Disable Dates 1.4.