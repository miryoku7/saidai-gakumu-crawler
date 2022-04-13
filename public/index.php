<?php

//=========================  CONFIG  =========================//
require_once "../vendor/autoload.php";
require "../app/functions/index.php";
require "../config.php";

use Goutte\Client;
use Symfony\Component\DomCrawler\Field\InputFormField;

$client = new Client();
$crawler = $client->request('GET', 'https://risyu.saitama-u.ac.jp/portal/LogIn.aspx');
$jsonPath = "../app/data/save.json";
$pdfPath = "./data/pdf/";

//=========================  LOGIN  =========================//
$loginForm = $crawler->selectButton('ログイン')->form();
$loginForm['txtID'] = USER_ID;
$loginForm['txtPassWord'] = PASSWORD;
$crawler = $client->submit($loginForm);


//=========================  MAIN-PAGE  =========================//
/////--------------------  getInfomation  --------------------/////

$html = $crawler->html();
$dom = new DOMDocument('1.0', 'UTF-8');
$html = mb_convert_encoding($html, "HTML-ENTITIES", 'auto');
@$dom->loadHTML($html);
$xpath = new DOMXPath($dom);
$xpath->registerNamespace("php", "http://php.net/xpath");
$xpath->registerPHPFunctions();


$allData = [];
for ($i=0; $i < 5; $i++) { 
    $data = [];
    $pi = $i + 2;
    $anchor = $xpath->query("//table[@id='ctl00_phContents_ucInfoList_GrdVwNotice']/tr[$pi]//a");
    $content = $xpath->query(
        "//table[@id='ctl00_phContents_ucInfoList_GrdVwNotice']/tr[$pi]/td[2]/*/div[2]"
    );

    $content = trim($content->item(0)->nodeValue);
    $content = preg_split('/\n|\r\n?/', $content);
    $content = implode("\n", array_map('trim', $content));
    $content = preg_replace("/\n\n\n/","\n", $content);
    $data['content'] =  $content;
    
    for ($i1=0; $i1 < $anchor->length; $i1++) { 
        $value = trim($anchor->item($i1)->nodeValue);
        if ( $value !== "" ) {

            if (strpos($value,'.pdf') !== false) {
                # PDFである時の処理

                $attr = $anchor->item($i1)->attributes;

                for ($i0=0; $i0 < $attr->length; $i0++) { 
                    if ($attr->item($i0)->name === "href") {
                        $href = $attr->item($i0)->nodeValue;
                        $href = urldecode($href);
                        $href = preg_replace("/javascript:__doPostBack\('/", "", $href);
                        $href = preg_replace("/',''\)/", "", $href);
                        $data['href'][$value] =  $href;
                    }
                }
                
            } else {
                # メッセージタイトル

                $data['title'] =  $value;
            }
        } 
    }
    $allData[] = $data;
}

/////----------------------  download PDF  ----------------------/////
foreach ($allData as $values) {

    if ($values["href"] ?? false) {
        foreach ($values["href"] as $name => $href) {
            if (!file_exists($pdfPath.$href."===SPLIT===".$name)){
                // 似たようなファイルがなければ 更新させる

                $loginForm = $crawler->filter('form')->form();
                $domdocument = new DOMDocument;
                $input = $domdocument->createElement('input');
                $input->setAttribute('name', '__EVENTTARGET');
                $input->setAttribute('value', $href);
                // $input->setAttribute('value', 'ctl00$phContents$ucInfoList$GrdVwNotice$ctl02$lnkTmp_2');
                $input = new InputFormField($input);
                $loginForm->set($input);
                $crawler = $client->submit($loginForm);
                print_r($crawler);
                $crawler = $client->getResponse()->getContent();
                file_put_contents($pdfPath.$href."===SPLIT===".$name, $crawler);


                $crawler = $client->back();
            }  

        }
    }
}


//=========================  SaveData  =========================//
$date = new DateTime();
$date =  $date->format(DateTime::ISO8601);

$saveData = [
    "data" => $allData,
    "date" => $date,
];


$json = file_get_contents($jsonPath); 
$prevJson = json_decode($json, true);


// 前のデータ(json)と比較して
// 最初のデータのタイトルが違えば更新。
if ($prevJson["data"][0]["title"] != $saveData["data"][0]["title"]) {
    $save = json_encode($saveData , JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
    file_put_contents($jsonPath , $save);

    // 更新あり
    // メールなどで PDF の path と共に通知する処理。

} else {

    // 更新なし
    echo "更新なし";
}