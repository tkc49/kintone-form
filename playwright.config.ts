import { defineConfig, devices } from '@playwright/test';

/**
 * Playwright configuration for Form data to kintone E2E tests.
 * @see https://playwright.dev/docs/test-configuration
 */
export default defineConfig({
	testDir: './tests/E2E',
	testMatch: '**/*.spec.ts',

	/* Run tests in files in parallel */
	fullyParallel: true,

	/* Fail the build on CI if you accidentally left test.only in the source code. */
	forbidOnly: !!process.env.CI,

	/* Retry on CI only */
	retries: process.env.CI ? 2 : 0,

	/* Opt out of parallel tests on CI. */
	workers: process.env.CI ? 1 : undefined,

	/* Reporter to use. See https://playwright.dev/docs/test-reporters */
	reporter: [
		['list'],
		['html', { open: 'never' }],
	],

	/* Shared settings for all the projects below. See https://playwright.dev/docs/api/class-testoptions. */
	use: {
		/* Base URL to use in actions like `await page.goto('/')`. */
		baseURL: 'http://localhost:8888',

		/* Collect trace when retrying the failed test. See https://playwright.dev/docs/trace-viewer */
		trace: 'on-first-retry',

		/* Take screenshot on failure */
		screenshot: 'only-on-failure',

		/* Video recording on failure */
		video: 'on-first-retry',
	},

	/* Configure projects for major browsers */
	projects: [
		{
			name: 'chromium',
			use: { ...devices['Desktop Chrome'] },
		},
		// Add Firefox and Safari for broader coverage if needed
		// {
		// 	name: 'firefox',
		// 	use: { ...devices['Desktop Firefox'] },
		// },
		// {
		// 	name: 'webkit',
		// 	use: { ...devices['Desktop Safari'] },
		// },
	],

	/* Timeout settings - more generous for CI environments */
	timeout: process.env.CI ? 60000 : 30000,
	expect: {
		timeout: process.env.CI ? 10000 : 5000,
	},

	/* Run your local dev server before starting the tests */
	// webServer: {
	// 	command: 'npm run wp-env start',
	// 	url: 'http://localhost:8888',
	// 	reuseExistingServer: !process.env.CI,
	// 	timeout: 120000,
	// },
});
