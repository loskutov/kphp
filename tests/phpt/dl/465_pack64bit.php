@ok
<?php

$p1 = unpack("P2", pack ('P*', 123, '8589934592'));
$j1 = unpack("J2", pack ('J*', 123, '8589934592'));
$q1 = unpack("Q2", pack ('Q*', 123, '8589934592'));
$p2 = unpack("P", pack("P", 0));
$j2 = unpack("J", pack("J", 0));
$q2 = unpack("Q", pack("Q", 0));

#ifndef KittenPHP
// KPHP вернёт строки, а не int-ы
$p1 = [1 => '123', 2 => '8589934592'];
$j1 = [1 => '123', 2 => '8589934592'];
$q1 = [1 => '123', 2 => '8589934592'];
$p2 = [1 => '0'];
$j2 = [1 => '0'];
$q2 = [1 => '0'];
#endif
var_dump($p1, $j1, $q1, $p2, $j2, $q2);

// Проверка на endianness
$p = pack("P", 1);
$j = pack("J", 1);
$q = pack("Q", 1);
// little endian
var_dump(to_printable_bytestring($p));
// big endian
var_dump(to_printable_bytestring($j));
// machine byte order
var_dump(to_printable_bytestring($q));

// Тесты на рандомную строчку, а не ту которую вернула pack функция
// каст к строке, чтобы привести всё к kphp поведению (строка вместо int-a)
var_dump((string) unpack("P", "Vasya go gulat"));
var_dump((string) unpack("J", "Vasya go gulat"));
var_dump((string) unpack("Q", "Vasya go gulat"));

// Проверка, что не упадём при строках < необходимой длины
// silent оператор т.к. в kphp к warning-у добавляется stacktrace для c++ кода
@unpack("P", "");
@unpack("P", "\x0\x5");
@unpack("P", "\x0\x5\x5\x5\x5\x5\x5");
@unpack("J", "");
@unpack("J", "\x0\x5");
@unpack("J", "\x0\x5\x5\x5\x5\x5\x5");
@unpack("Q", "");
@unpack("Q", "\x0\x5");
@unpack("Q", "\x0\x5\x5\x5\x5\x5\x5");

// вспомогательная функция для вывода байтов строки
function to_printable_bytestring($str) {
  $bytes = [];
  for ($i = 0; $i < strlen($str); $i++) {
    $bytes[] = ord($str[$i]);
  }

  return implode('_', $bytes);
}