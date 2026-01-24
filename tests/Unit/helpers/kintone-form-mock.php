<?php
/**
 * Kintone_Form and Contact Form 7 mocks for unit testing.
 *
 * @package Kintone_Form
 */

if ( ! class_exists( 'Kintone_Form' ) ) {
	/**
	 * Mock Kintone_Form class.
	 */
	class Kintone_Form {
		/**
		 * Get CF7 special mail tags.
		 *
		 * @return array Array of special tag names.
		 */
		public static function get_cf7_special_tags() {
			return array(
				'_remote_ip',
				'_user_agent',
				'_url',
				'_date',
				'_time',
				'_post_id',
				'_post_name',
				'_post_title',
				'_post_url',
				'_post_author',
				'_post_author_email',
				'_site_title',
				'_site_description',
				'_site_url',
				'_site_admin_email',
				'_invalid_fields',
				'_serial_number',
			);
		}
	}
}

if ( ! class_exists( 'WPCF7_FormTag' ) ) {
	/**
	 * Mock WPCF7_FormTag class.
	 */
	class WPCF7_FormTag {
		/**
		 * Form tag type.
		 *
		 * @var string
		 */
		public $type = '';

		/**
		 * Form tag content.
		 *
		 * @var string
		 */
		public $content = '';
	}
}

if ( ! class_exists( 'WPCF7_MailTag' ) ) {
	/**
	 * Mock WPCF7_MailTag class.
	 */
	class WPCF7_MailTag {
		/**
		 * Tag string.
		 *
		 * @var string
		 */
		public $tag;

		/**
		 * Tag name.
		 *
		 * @var string
		 */
		public $tag_name;

		/**
		 * Values.
		 *
		 * @var string
		 */
		public $values;

		/**
		 * Constructor.
		 *
		 * @param string $tag      Tag string.
		 * @param string $tag_name Tag name.
		 * @param string $values   Values.
		 */
		public function __construct( $tag, $tag_name, $values ) {
			$this->tag      = $tag;
			$this->tag_name = $tag_name;
			$this->values   = $values;
		}

		/**
		 * Get corresponding form tag.
		 *
		 * @return WPCF7_FormTag|null Form tag or null.
		 */
		public function corresponding_form_tag() {
			// Return null in tests - the check_acceptance function handles this.
			return null;
		}
	}
}

if ( ! class_exists( 'WPCF7_Submission' ) ) {
	/**
	 * Mock WPCF7_Submission class.
	 */
	class WPCF7_Submission {
		/**
		 * Get instance.
		 *
		 * @return null Always returns null in tests.
		 */
		public static function get_instance() {
			return null;
		}
	}
}

if ( ! class_exists( 'WPCF7_ContactForm' ) ) {
	/**
	 * Mock WPCF7_ContactForm class.
	 */
	class WPCF7_ContactForm {
		/**
		 * Get current contact form.
		 *
		 * @return null Always returns null in tests.
		 */
		public static function get_current() {
			return null;
		}
	}
}
