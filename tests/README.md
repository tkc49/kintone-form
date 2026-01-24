# テスト

Form data to kintone プラグインのテストスイート。

## ディレクトリ構成

```
tests/
├── Unit/                    # ユニットテスト（WordPress 不要）
│   ├── helpers/             # テストヘルパー・モック
│   ├── ModulesTest.php      # モジュール変換テスト
│   └── UtilityTest.php      # ユーティリティテスト
├── E2E/                     # Playwright E2E テスト
│   ├── basic-form.spec.ts   # 基本フォームテスト
│   └── field-types.spec.ts  # フィールドタイプテスト
├── fixtures/                # テストフィクスチャ
│   └── uploads/             # アップロードファイル用
├── bootstrap.php            # PHPUnit ブートストラップ
├── bootstrap-unit.php       # ユニットテスト用ブートストラップ
└── test-kintone-form.php    # レガシー統合テスト
```

## ユニットテスト実行

```bash
# 依存関係インストール
composer install

# ユニットテスト実行
composer test

# カバレッジ付き
./vendor/bin/phpunit --testsuite Unit --coverage-html coverage
```

## E2E テスト実行

```bash
# 依存関係インストール
npm install

# WordPress 環境起動
npm run wp-env start

# Playwright テスト実行
npm run test:e2e

# UIモードで実行
npm run test:e2e:ui

# WordPress 環境停止
npm run wp-env stop
```

## 静的解析

```bash
# PHPCS
composer phpcs

# PHPStan
composer phpstan
```

## CI/CD

GitHub Actions で自動実行:

- **PHPUnit**: PHP 8.1, 8.2, 8.3 でユニットテスト
- **PHPCS**: コーディング規約チェック
- **PHPStan**: 静的解析
- **Playwright**: E2E テスト
- **Plugin Check**: WordPress.org プラグインチェック

## テスト戦略

### ユニットテスト

- WordPress 依存なしでモジュールのデータ変換ロジックをテスト
- `WP_Error`、`Kintone_Form` などはモックで代用
- CI で高速に実行可能

### E2E テスト

- `wp-env` で WordPress 環境を構築
- Playwright で実際のブラウザ操作をテスト
- kintone API はモック（CI では実際の kintone に接続しない）

### ローカル探索的テスト

- 実際の kintone 環境との統合確認
- Claude in Chrome + kintone MCP を使用
- リリース前のマニュアル確認
