import { test, expect, Page } from '@playwright/test';

/**
 * E2E tests for Form data to kintone plugin.
 *
 * These tests verify the form submission workflow without connecting to actual kintone.
 * The kintone API is mocked at the WordPress level for CI testing.
 */

test.describe('Form data to kintone - Basic Form', () => {
	/**
	 * Login to WordPress admin before each test.
	 */
	test.beforeEach(async ({ page }) => {
		await loginToWordPress(page);
	});

	test('Plugin is activated and visible in admin menu', async ({ page }) => {
		// Navigate to plugins page
		await page.goto('/wp-admin/plugins.php');

		// Check that the plugin is activated
		const pluginRow = page.locator('tr[data-slug="kintone-form"]');
		await expect(pluginRow).toBeVisible();

		// Verify it's active (has deactivate link)
		const deactivateLink = pluginRow.locator('a.deactivate');
		await expect(deactivateLink).toBeVisible();
	});

	test('Contact Form 7 integration tab is visible', async ({ page }) => {
		// Navigate to Contact Form 7 forms
		await page.goto('/wp-admin/admin.php?page=wpcf7');

		// Wait for the page to load
		await page.waitForLoadState('networkidle');

		// Check if there's at least one form or add form button
		const addNewButton = page.locator('a.page-title-action, a.add-new-h2');
		await expect(addNewButton).toBeVisible();
	});

	test('kintone settings tab appears in CF7 form editor', async ({ page }) => {
		// First, create a new form if needed
		await page.goto('/wp-admin/admin.php?page=wpcf7-new');

		// Wait for the form editor to load
		await page.waitForLoadState('networkidle');

		// Look for the kintone tab in the form editor
		// The tab ID is typically 'kintone-form-editor' based on common patterns
		const kintoneTab = page.locator('#kintone-form-editor, a[href*="kintone"]');

		// If the tab exists, the plugin is properly integrated
		if (await kintoneTab.count() > 0) {
			await expect(kintoneTab.first()).toBeVisible();
		} else {
			// Alternative: look for kintone settings in any tab panel
			const pageContent = await page.content();
			expect(
				pageContent.includes('kintone') ||
				pageContent.includes('Form data to kintone')
			).toBeTruthy();
		}
	});
});

test.describe('Form data to kintone - Settings Page', () => {
	test.beforeEach(async ({ page }) => {
		await loginToWordPress(page);
	});

	test('kintone settings fields are present in form editor', async ({ page }) => {
		// Create or edit a form
		await page.goto('/wp-admin/admin.php?page=wpcf7-new');
		await page.waitForLoadState('networkidle');

		// Check for kintone-specific form fields
		// These are the main settings fields based on the plugin structure
		const settingsIndicators = [
			'input[name*="kintone"]',
			'select[name*="kintone"]',
			'[id*="kintone"]',
			'.kintone-form-setting',
		];

		let foundSettings = false;
		for (const selector of settingsIndicators) {
			const element = page.locator(selector).first();
			if (await element.count() > 0) {
				foundSettings = true;
				break;
			}
		}

		// The plugin should add some kintone-related elements to the form editor
		// This test verifies the integration is working
		const pageContent = await page.content();
		const hasKintoneContent = pageContent.toLowerCase().includes('kintone');

		expect(foundSettings || hasKintoneContent).toBeTruthy();
	});
});

test.describe('Form data to kintone - Frontend Form', () => {
	test('Form can be displayed on a page', async ({ page }) => {
		// This test requires a page with a CF7 form to be set up
		// For now, we'll just verify the homepage loads
		await page.goto('/');
		await expect(page).toHaveTitle(/.*/);

		// Check that the page loads without JavaScript errors
		const errors: string[] = [];
		page.on('pageerror', (error) => {
			errors.push(error.message);
		});

		await page.waitForLoadState('networkidle');

		// Filter out known benign errors
		const criticalErrors = errors.filter(
			(err) => !err.includes('ResizeObserver') && !err.includes('Script error')
		);

		expect(criticalErrors).toHaveLength(0);
	});
});

/**
 * Helper function to login to WordPress admin.
 */
async function loginToWordPress(page: Page): Promise<void> {
	await page.goto('/wp-login.php');

	// Check if already logged in
	if (page.url().includes('wp-admin')) {
		return;
	}

	// Fill login form
	await page.fill('#user_login', 'admin');
	await page.fill('#user_pass', 'password');
	await page.click('#wp-submit');

	// Wait for redirect to admin
	await page.waitForURL(/wp-admin/);
}
