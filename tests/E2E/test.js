const puppeteer = require( 'puppeteer' );
( async () => {
	const browser = await puppeteer.launch( { headless: false } )
	const page    = await browser.newPage()

	await page.goto( 'http://form-data-to-kintone.test/form-data-to-kintone-test' )

	await page.setViewport( {
		width : 1900,
		height: 1000
	} )

	await page.waitForSelector( '.wpcf7-submit' )

	await page.waitForSelector( '[name="your-text"]' )
	await page.click( '[name="your-text"]' )
	await page.type( '[name="your-text"]', '細谷　崇' )

	await page.waitForSelector( '[name="your-rich"]' )
	await page.click( '[name="your-rich"]' )
	await page.type( '[name="your-rich"]', '細谷　崇1\r細谷　崇1\r細谷　崇1' )

	await page.waitForSelector( '[name="your-multi"]' )
	await page.click( '[name="your-multi"]' )
	await page.type( '[name="your-multi"]', '細谷　崇2\r細谷　崇2\r細谷　崇2' )

	await page.waitForSelector( '[name="your-number"]' )
	await page.click( '[name="your-number"]' )
	await page.type( '[name="your-number"]', '1000' )

	await page.waitForSelector( '[name="your-radio"]' )
	await page.click( '[name="your-radio"][value="sample2"]' )

	await page.waitForSelector( '[name="your-checkbox[]"]' )
	await page.click( '[name="your-checkbox[]"][value="sample1"]' )

	await page.waitForSelector( '[name="your-checkbox[]"]' )
	await page.click( '[name="your-checkbox[]"][value="sample2"]' )

	await page.select( '[name="your-select[]"]', 'sample1', 'sample2' )

	await page.select( '[name="your-drop"]', 'sample1' )

	await page.type( '[name="your-date"]', '002019/09/09' )
	await page.type( '[name="your-link"]', 'https://yahoo.co.jp' )


	await page.click( '.wpcf7-submit' )
	await page.waitForSelector( '.wpcf7-mail-sent-ok' )


	await browser.close()
} )()

