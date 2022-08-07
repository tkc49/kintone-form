=== Form data to kintone ===
Contributors: tkc49, agunchar, matsuoatsushi
Tags: cybozu, kintone, crm, database, custom field, contact form 7, form
Requires at least: 4.9
Tested up to: 5.8
Stable tag: 2.26.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This plugin can save form data to kintone.

== Description ==

This plugin is add-on of Contact Form 7 plugin.
This plugin can save form data to kintone.

[youtube https://www.youtube.com/watch?v=Z_5cETJyA_w]


= What is kintone? =

It is a cloud service that can make the business applications with non-programming provided by Cybozu.

Collaborate with team members and partners via apps and workspaces.

* Information in Japanese : [https://kintone.cybozu.com/jp/](https://kintone.cybozu.com/jp/)
* Information in English: [https://www.kintone.com/](https://www.kintone.com/)

Thanks
Cover banner designed by [akari_doi](https://profiles.wordpress.org/akari_doi/)


== Installation ==

1. Upload the entire `kintone-form` folder to the `/ wp-content / plugins /` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress

A tab for setting the information on kitone appears on the "Contact Form 7" setting screen.

== Frequently asked questions ==


== Screenshots ==

1. screenshot-1.png

== Changelog ==

= 2.26.0 =
Release Date: August 7th, 2022

*[Added] Support not to save to kintone when demo_mode or do_not_store is set.

= 2.25.0 =
Release Date: Jun 23th, 2022

*[Added] Added 'form_data_to_kintone_completed_saving' action hook.

= 2.24.5 =
Release Date: May 31th, 2022

*[Fixed] Warning error.

= 2.24.4 =
Release Date: May 28th, 2022

*[Fixed] Warning error.

= 2.24.3 =
Release Date: March 30th, 2022

* [Fixed] Added handling of missing attachments.

= 2.24.2 =
Release Date: March 3th, 2022

* [Fixed] Fixed a bug where the "quoteattr" function did not exist.

= 2.24.1 =
Release Date: February 20th, 2022

* [Fixed] Fixed a forgotten version upgrade.

= 2.24.0 =
Release Date: February 20th, 2022

* [Added] Added the feature to import attached files.
* [Changed] Changed kintone_form_attachments_data filter to deprecated.
* [Tested] Contact form 7 version's 5.5.5

= 2.23.0 =
Release Date: January 16th, 2022

* [Changed] Changed to save form values as blank if they are blank.

= 2.22.3 =
Release Date: December 26th, 2021

* [Fixed] Fix .distignore file.

= 2.22.2 =
Release Date: December 26th, 2021

* [TEST] Deploy test.

= 2.22.1 =
Release Date: December 26th, 2021

* [TEST] Deploy test.

= 2.22.0 =
Release Date: December 3th, 2021

* [Fixed] Supported for Contact form 7 5.5.3.

= 2.21.0 =
Release Date: August 25th, 2021

* [Add] Added support for importing organization selection into kintone.

= 2.20.0 =
Release Date: January 31th, 2021

* [Changed] Changed the way to get checkbox data when using "Contact Form 7 Multi-Step Forms" due to changes in "Contact form 7"

= 2.19.0 =
Release Date: January 19th, 2021

* [Fixed] Fixed a bug where the settings would disappear when data could not be retrieved from kintone when saving in the kintone tab of CF7 in the administration screen

= 2.18.1 =
Release Date: December 23th, 2020

* [Change] update version

= 2.18.0 =
Release Date: December 23th, 2020

* [Add] Add "_date" with special tags
* [Add] Add "_time" with special tags

= 2.17.4 =
* [Fix] Change type from const to let for IE11

= 2.17.3 =
* [Add] Added the ability to save logged-in user information to kintone.

= 2.17.2 =
* [Fix] Fix bug.

= 2.17.1 =
* [Remove] Remove the ability to save logged-in user information to kintone.

= 2.17.0 =
* [Add] Added the ability to save logged-in user information to kintone.

= 2.16.4 =
* [Fix] Changing parameters of wpcf7_special_mail_tags filter in Contact form 7.

= 2.16.3 =
* [Fix] a bug form_data_to_kintone_saved filter's parameter.

= 2.16.2 =
* [Fix] a bug in the numerical judgment.

= 2.16.1 =
* [Change] "form_data_to_kintone_kintone_post_url" to Add parameters
* [Change] private to public static of "get_data_for_post"

= 2.16.0 =
* [Add] filter hook of "form_data_to_kintone_kintone_post_url"

= 2.15.1 =
* [fix] readme.txt of ChangeLog.

= 2.15.0 =
* [add] filter hook of "form_data_to_kintone_get_update_key_for_add_on_enable_update_key"
* [add] filter hook of "form_data_to_kintone_retry_save"
* [add] filter hook of "form_data_to_kintone_reset_data"

= 2.14.3 =
* Fix "Not Support" message display problem.

= 2.14.2 =
* Forget count up of version.

= 2.14.1 =
* Fix a bug where kintone is not updated when the number is 0.

= 2.14.0 =
* Add filter kintone_form_add_original_cf7_mail_tag_for_kintone_form, kintone_form_cf7_posted_data_before_post_to_kintone

= 2.13.1 =
* Adjusted the kintone label area on the settings screen because it is too small.

= 2.13.0 =
* Support for the "Contact Form 7 Multi-Step Forms" plugin.

= 2.12.1 =
* Change version.

= 2.12.0 =
* Fix the error of the value of radio element when using "Contact Form 7 Multi-Step Forms".

= 2.11.0 =
* Add feature of subtable.

= 2.10.1 =
* Fix Removing quotation escapes.

= 2.10.0 =
* Add customize mailtag on datetime, time.

= 2.9.0 =
* New – Add guest space.

= 2.8.2 =
* Fix contents of Error message

= 2.8.1 =
* Fix bug Datetime format error of datetime.php

= 2.8.0 =
* Add filter_hook of form_data_to_kintone_saved

= 2.7.1 =
* Fix displaying NULL in error mail

= 2.7.0 =
* Add special mail tags
  * remote_ip
  * user_agent
  * url
  * post_id
  * post_name
  * post_title
  * post_url
  * post_author
  * post_author_email
  * site_title
  * site_description
  * site_url
  * site_admin_email

= 2.6.5 =
* Change version

= 2.6.4 =
* Add Contributor.

= 2.6.3 =
* Fix Fixed a bug what datetime formatting(UTC->ISO 8601).

= 2.6.2 =
* Fix Fixed a bug when deleting an app in the multi-app setting.

= 2.6.1 =
* Fix doesn't work when 0 of number.

= 2.6.0 =
* Add CF7 form title to error message.

= 2.5.0 =
* Change remove checking require by plugin.

= 2.4.0 =
* Change checking of KintoneForm_drop_down.

= 2.3.2 =
* Change all of Class name.

= 2.3.1 =
* Change Admin of Class name to kintone_Form_Admin.

= 2.3.0 =
* Refactoring

= 2.2.5 =
* Change readme.txt

= 2.2.4 =
* Add put post_title to kintone

= 2.2.3 =
* Changed test up version

= 2.2.2 =
* Fix typo

= 2.2.1 =
* Fix typo

= 2.2.0 =
* Add multi language

= 2.1.6 =
* Change readme.txt

= 2.1.5 =
* Change readme.txt

= 2.1.4 =
* fix notice error

= 2.1.3 =
* Change: Change error message mail.

= 2.1.2 =
* Fix bug: Change wpcf7_submit to wpcf7_mail_sent

= 2.1.1 =
* add form_data_to_kintone_get_unique_key to apply_filters

= 2.0.1 =
* fix some

= 2.0.0 =
* add kintone Basic Authentication
* fix some bug

= 1.10.2 =
* fix add myloadmore.js

= 1.10.1 =
* fix version number

= 1.10.0 =
* changes some setting page

= 1.0.18 =
* fix bug

= 1.0.17 =
* fix CF7 Radio Button

= 1.0.16 =
* Add time of mailtag

= 1.0.15 =
* It corresponds to hidden of CF7

= 1.0.14 =
* Fixed bug that unnecessary error mail was sent

= 1.0.13 =
* fix time format error

= 1.0.12 =
* change plugin's icon

= 1.0.11 =
* add youtube in readme.txt

= 1.0.10 =
* fix Saving kintone's configuration information as null results in an error

= 1.0.9 =
* Add add-ons

= 1.0.8 =
* remove add-ons text

= 1.0.7 =
* fix a problem with range

= 1.0.6 =
* remove admin menu

= 1.0.5 =
* fix

= 1.0.4 =
* add contributer

= 1.0.3 =
* Change plugin's discription

= 1.0.2 =
* Change plugin's discription

= 1.0.1 =
* Change plugin name

= 1.0.0 =
* First Release
