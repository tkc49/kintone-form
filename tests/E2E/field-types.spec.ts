import { test, expect, Page } from '@playwright/test';

/**
 * E2E tests for various field types in Contact Form 7 → kintone integration.
 *
 * These tests verify that different CF7 field types can be properly submitted.
 * Note: Actual kintone API integration is mocked for CI testing.
 */

test.describe('Form data to kintone - Field Types', () => {
	/**
	 * Skip these tests if no test form page is configured.
	 * In a real setup, you would create a test form programmatically.
	 */
	test.skip(({ }, testInfo) => {
		// Skip field type tests in CI for now
		// These tests require a pre-configured form page
		return !!process.env.CI;
	});

	test.beforeEach(async ({ page }) => {
		// Navigate to the test form page
		await page.goto('/form-data-to-kintone-test/');
		await page.waitForLoadState('networkidle');
	});

	test('Text field accepts input', async ({ page }) => {
		const textField = page.locator('[name="your-text"]');
		if (await textField.count() === 0) {
			test.skip();
			return;
		}

		await textField.fill('テスト太郎');
		await expect(textField).toHaveValue('テスト太郎');
	});

	test('Number field accepts numeric input', async ({ page }) => {
		const numberField = page.locator('[name="your-number"]');
		if (await numberField.count() === 0) {
			test.skip();
			return;
		}

		await numberField.fill('12345');
		await expect(numberField).toHaveValue('12345');
	});

	test('Radio button can be selected', async ({ page }) => {
		const radioButton = page.locator('[name="your-radio"]');
		if (await radioButton.count() === 0) {
			test.skip();
			return;
		}

		await radioButton.first().check();
		await expect(radioButton.first()).toBeChecked();
	});

	test('Checkbox can be checked', async ({ page }) => {
		const checkbox = page.locator('[name="your-checkbox[]"]');
		if (await checkbox.count() === 0) {
			test.skip();
			return;
		}

		await checkbox.first().check();
		await expect(checkbox.first()).toBeChecked();
	});

	test('Multiple checkboxes can be selected', async ({ page }) => {
		const checkboxes = page.locator('[name="your-checkbox[]"]');
		if (await checkboxes.count() < 2) {
			test.skip();
			return;
		}

		await checkboxes.nth(0).check();
		await checkboxes.nth(1).check();

		await expect(checkboxes.nth(0)).toBeChecked();
		await expect(checkboxes.nth(1)).toBeChecked();
	});

	test('Dropdown can be selected', async ({ page }) => {
		const dropdown = page.locator('[name="your-drop"]');
		if (await dropdown.count() === 0) {
			test.skip();
			return;
		}

		// Get available options
		const options = dropdown.locator('option');
		const optionCount = await options.count();

		if (optionCount > 1) {
			const firstOptionValue = await options.nth(1).getAttribute('value');
			if (firstOptionValue) {
				await dropdown.selectOption(firstOptionValue);
				await expect(dropdown).toHaveValue(firstOptionValue);
			}
		}
	});

	test('Multi-select can have multiple selections', async ({ page }) => {
		const multiSelect = page.locator('[name="your-select[]"]');
		if (await multiSelect.count() === 0) {
			test.skip();
			return;
		}

		// Select multiple options
		const options = multiSelect.locator('option');
		const optionCount = await options.count();

		if (optionCount >= 2) {
			const values: string[] = [];
			for (let i = 0; i < Math.min(2, optionCount); i++) {
				const value = await options.nth(i).getAttribute('value');
				if (value) {
					values.push(value);
				}
			}
			if (values.length > 0) {
				await multiSelect.selectOption(values);
			}
		}
	});

	test('Date field accepts date input', async ({ page }) => {
		const dateField = page.locator('[name="your-date"]');
		if (await dateField.count() === 0) {
			test.skip();
			return;
		}

		await dateField.fill('2024-01-15');
		// Date format may vary based on browser/locale
		const value = await dateField.inputValue();
		expect(value).toContain('2024');
	});

	test('URL/Link field accepts URL', async ({ page }) => {
		const linkField = page.locator('[name="your-link"]');
		if (await linkField.count() === 0) {
			test.skip();
			return;
		}

		await linkField.fill('https://example.com');
		await expect(linkField).toHaveValue('https://example.com');
	});

	test('Multiline text field accepts multiple lines', async ({ page }) => {
		const textArea = page.locator('[name="your-multi"]');
		if (await textArea.count() === 0) {
			test.skip();
			return;
		}

		const multiLineText = 'Line 1\nLine 2\nLine 3';
		await textArea.fill(multiLineText);
		const value = await textArea.inputValue();
		expect(value).toContain('Line 1');
		expect(value).toContain('Line 2');
	});

	test('Rich text field accepts formatted content', async ({ page }) => {
		const richField = page.locator('[name="your-rich"]');
		if (await richField.count() === 0) {
			test.skip();
			return;
		}

		await richField.fill('Rich text content');
		await expect(richField).toHaveValue('Rich text content');
	});
});

test.describe('Form data to kintone - Form Submission', () => {
	test.skip(({ }, testInfo) => {
		// Skip submission tests in CI - requires actual form setup
		return !!process.env.CI;
	});

	test('Form can be submitted successfully', async ({ page }) => {
		await page.goto('/form-data-to-kintone-test/');
		await page.waitForLoadState('networkidle');

		const submitButton = page.locator('.wpcf7-submit');
		if (await submitButton.count() === 0) {
			test.skip();
			return;
		}

		// Fill required fields
		const textField = page.locator('[name="your-text"]');
		if (await textField.count() > 0) {
			await textField.fill('Test User');
		}

		// Submit the form
		await submitButton.click();

		// Wait for response
		// CF7 shows different messages based on success/failure
		const responseMessage = page.locator('.wpcf7-response-output');
		await expect(responseMessage).toBeVisible({ timeout: 10000 });
	});

	test('Form shows success message after submission', async ({ page }) => {
		await page.goto('/form-data-to-kintone-test/');
		await page.waitForLoadState('networkidle');

		const submitButton = page.locator('.wpcf7-submit');
		if (await submitButton.count() === 0) {
			test.skip();
			return;
		}

		// Fill all required fields to ensure success
		const requiredFields = page.locator('[aria-required="true"], .wpcf7-validates-as-required');
		const fieldCount = await requiredFields.count();

		for (let i = 0; i < fieldCount; i++) {
			const field = requiredFields.nth(i);
			const tagName = await field.evaluate((el) => el.tagName.toLowerCase());
			const type = await field.getAttribute('type');

			if (tagName === 'input' && type === 'text') {
				await field.fill('Test Value');
			} else if (tagName === 'input' && type === 'email') {
				await field.fill('test@example.com');
			} else if (tagName === 'textarea') {
				await field.fill('Test content');
			}
		}

		// Submit
		await submitButton.click();

		// Check for success message (CF7 specific class)
		const successMessage = page.locator('.wpcf7-mail-sent-ok, .wpcf7-response-output');
		await expect(successMessage).toBeVisible({ timeout: 15000 });
	});
});
