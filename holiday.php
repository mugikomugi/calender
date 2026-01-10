<?php
//祝日自動判定
//外部ファイルから祝日データを取得
$fp = fopen('https://holidays-jp.github.io/api/v1/' . 'date.csv', 'r');
if ($fp === false) {
  die('ファイルの読み込みに失敗しました');
}

$hoiliday_days = [];
//今年の祝日を読み込む
while ($row = fgetcsv($fp)) {
  $parts = htmlspecialchars($row[0], ENT_QUOTES, 'UTF-8');
  //今年の分を取得して月と日付の文字列を整形
  $part = substr($parts, 0, 4);
  if ($part === $y) {
    $hoiliday_days[] = str_replace(['-'], '', substr($parts, -5));
  }
}

// 12月なら翌年の1月分も読み込む
if ((int)$m === 12) {
  rewind($fp);
  // ファイルポインタを先頭に戻す
  while ($row = fgetcsv($fp)) {
    $parts = htmlspecialchars($row[0], ENT_QUOTES, 'UTF-8');
    $year = substr($parts, 0, 4);
    $month = substr($parts, 5, 2);
    if ($year == $y + 1 && $month == '01') {
      $hoiliday_days[] = str_replace('-', '', substr($parts, -5));
    }
  }
}

fclose($fp);

$result = [];
foreach ($hoiliday_days as $value) {
  // 先頭2桁を取得
  $key = substr($value, 0, 2); // グループに追加
  $value = substr($value, -2);
  $result[$key][] = (int) $value;
}

//祝日の配列1
$holiday = [];

//for文の最初は;で省略できる、ただし$dは外で定義済みの必要あり
for (; $d <= $lastday; $d++) {
  $holiday[$d] = in_array($d, [
    1 => $result['01'],
    2 => $result['02'],
    3 => $result['03'],
    4 => $result['04'],
    5 => $result['05'],
    6 => [],
    7 => $result['07'],
    8 => $result['08'],
    9 => $result['09'],
    10 => $result['10'],
    11 => $result['11'],
    12 => [],
  ][(int) $m]) ? 'holiday' : 'none';
}

//Qitaから教えてもらったコード
/* 手動で書き込む旧ver.
for(; $d <= $lastday; $d++) {
    $holiday[$d] = in_array($d, [
         1 => [1, 8],
         2 => [11, 12],
         3 => [21],
         4 => [29, 30],
         5 => [3, 4, 5],
         6 => [],
         7 => [23, 24],
         8 => [10],
         9 => [21, 22],
        10 => [],
        11 => [3, 23],
        12 => [],
    ][(int) $m]) ? 'holiday' : 'none';
}
*/
