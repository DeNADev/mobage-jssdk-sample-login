mobage-jssdk-sample-login
=========================
## 動作確認環境

* Mac Book Air OS 10.9.2
* PHP 5.4.24
* MySQL 5.6.17
* [jwt](https://github.com/F21/jwt) ライブラリ（PHP 5.4.8 以上必要）

※ JSSDKではDOM-based XSSに対するセキュリティ上の対策としてContent Security Policyを導入しており、それによってinlineなJavaScriptの実行を禁止しています。Chromeのextensionの中にはこちらに抵触する処理を行うものがあるため、PCなどのChromeブラウザにて試す際には、Secret Modeで実行してください。

## このサンプルの目的
Mobage JS SDK を利用したMobage Connect へのログインに必要な処理をイメージしやすいように実装したサンプルです。

これら3つで構成されています。
* 画面遷移せずにログインする Hybrid Flow
* 旧来通り画面遷移してログインする Authorization Code Grant Flow
* Game Server を用いない Client-Side Flow

### 関連ドキュメント
こちらではサンプルを動かす簡単な手順をまとめていますが、Mobage JS SDK の詳細については「[Mobage JS SDK 開発ドキュメント](https://docs.mobage.com/display/JPJSSDK/Guide_for_Engineers)」を参照してください。

## サンプルを動かすまでの手順
### サンプルをインストールする
このサンプルは PHP のファイル群から構成されています。PHP が実行されるディレクトリにコピーします。Web サーバーの設定により異なりますが、デフォルトの Mac 環境では下記ディレクトリに移動します。
```
sudo mv mobage-jssdk-sample-login /Library/WebServer/Documents/
```
#### Mac 環境でPHPがオフになっている方
デフォルトの Mac 環境で PHP がオフになっている方は、下記を実施してください。
* PHPがインストールされているか確認
```
$ php -v
```

* Apache の設定ファイルの権限変更
```
$ cd /etc/apache2
$ sudo chmod 644 httpd.conf
```

* PHP モジュールを有効にする
```
$ sudo vi httpd.conf
```

* 以下の# を削除して保存する
```
#LoadModule php5_module libexec/apache2/libphp5.so
```

* 再起動する
```
sudo apachectl restart
```

### developer.dena.jpにてアプリケーションを登録する
+ SPWebの「ゲームアーキテクチャ」を「Mobage Connect」に設定する  
+ 「Mobage Connect 情報」の横にある「情報を変更」ボタンを押して、「基本設定」＞「Sandbox用」の値を下記のように変更する（デフォルトディレクトリ以外にサンプルをおいた場合、適宜変更してください）  

| Setting | URI |
|:---|:---|
|Client URI        | http://localhost/mobage-jssdk-sample-login |
|Redirect URI      | http://localhost/mobage-jssdk-sample-login/hybrid_flow/login_cb_post.php <br> http://localhost/mobage-jssdk-sample-login/authorization_code_grant_flow/login_cb_get.php <br> http://localhost/mobage-jssdk-sample-login/client_flow/login_client.html|
|Client Origin URI | http://localhost |
|Post Logout Redirect URI |http://localhost/mobage-jssdk-sample-login |


### config.phpを編集する
#### BASE_URIについて
+ BASE_URIは必ず
"http://localhost"
のように通信方式とホスト名のみで記載してください。
+ もしデフォルトディレクトリ以外にサンプルコードを配置した場合、適宜変更してください。

#### CLIENT_ID/CLIENT_SECRETについて
Mobage Developers Japanで発行されたClient_ID, Client_Secretを記載してください。

* SPWEB_CLIENT_ID / SPWEB_CLIENT_SECRET
 * スマートフォンブラウザ環境での CLIENT_ID / CLIENT_SECRET です。
* ANDROID_CLIENT_ID / ANDROID_CLIENT_SECRET
 * Shell App SDK for Android 環境での CLIENT_ID / CLIENT_SECRET です。
* IOS_CLIENT_ID / IOS_CLIENT_SECRET
 * Shell App SDK for iOS 環境での CLIENT_ID / CLIENT_SECRET です。

#### URI設定について
CLIENT_URI, REDIRECT_POST_ENDPOINT, REDIRECT_GET_ENDPOINTなどの値は、
Mobage Developers Japanであらかじめ登録した値を記載してください。
もしデフォルトディレクトリ以外にサンプルコードを置いた場合、こちらは適宜変更してください。


### main.jsを読み込む
Mobage JS SDK とサンプル付属の main.js は、以下のように各ページの最下部で読み込まれています。なお、main.js の読み込みが最後になるようにしてください。

```
<script type='text/javascript'>
		(function() {
			var script   = document.createElement('script');
			script.type  = 'text/javascript';
			script.async = true;
			script.src   = "<?php echo JSSDK_PATH ?>";
			document.getElementsByTagName('script')[0].parentNode.appendChild(script);
		})();
</script>
<script type='text/javascript' src="main.js?time=<?php echo time() ?>"></script>
```
また、開発環境でJavaScriptがキャッシュされないように「?time=<?php echo time() ?>」を追加しています。

### JWTの検証用ライブラリを配置する
公開鍵 X509 formatで以下のライブラリを利用しています。  
https://github.com/F21/jwt

こちらのライブラリをダウンロードして以下のように配置してください。  
なお、こちらのライブラリを動作させるためには PHP 5.4.8 以上が必要です。

```
mobage-jssdk-sample-login/JWT/JWT.php
```

### mobage-jssdk-sample-login/client_flow/login_client.htmlを編集する
Client-Side フローではゲームサーバを利用しないログイン機能を提供するため、
このサンプルではHTMLファイルとJavaScriptのみで提供しています。
HTMLファイルに必要な CLIENT_ID と REDIRECT_URI を直接書き込んでください。

## 動作確認
ログインのTopページにアクセスして、各種ログインフローのリンク先からログインの動作確認をします。
（このサンプルでは、以下のURIにアクセスすると良いです。）

http://localhost/mobage-jssdk-sample-login/

※実機でなくエミュレーターで確認すると、意図しないUser Agentの変更が理由でログイン出来なくなることがあります。

※Client-Side Flowのみゲームサーバを利用しないログイン/ログアウトなので、他のフローと絡めて利用しないでください。
