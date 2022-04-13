# 埼大 学務システム クロール
**README 書き途中です!**

[埼玉大学務システム](https://risyu.saitama-u.ac.jp)のクローリングをするサンプルです。


## サンプルの説明
埼玉大学務システム後サンプルとしてログイン後、トップにあるお知らせを取得し、更新があった場合メール等で通知出来ます。

## インストール方法
```bash
composer install
```
`config.php` にて Idとパスワードを設定してください

`app` ディレクトリは 埼大生以外の方が観れないように 非公開ディレクトリにすべきです。

`public/index.php`にアクセスすると実行されるので、気になるならばindex.phpは非公開にしましょう。

## データの保存方法等

pdf以外の取得したデータは`app/data/save.json`に、
pdfは`public/pdf/`に保存してます。


jsonの中の`href`はPDFのpathや名前が格納してあります。

[`href`の値 + `href`のプロパティ] の名前で
`public/pdf/`に保存しています。



## Requirement
`php >= 8.0.2`

## License

This sample is under [MIT license](https://en.wikipedia.org/wiki/MIT_License).

ライセンスの書き方がよくわかりませんが、自由に使っていいです。
