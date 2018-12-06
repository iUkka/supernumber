<?php 

if($_POST['src'] == 666) {
  header("Content-type: text/plain; charset=utf-8");
  $str = join("", file("supernumber.php"));
  echo $str;
  exit();
}

$prefix = $_GET['prefix'];
$start = $_GET['start'];
$pages = $_GET['pages'];
$mode = $_GET['mode'];

if($prefix == '' || $start == 0 || $pages < 1 || $pages > 100 || $mode == 0) {
  header("Content-type: text/html; charset=utf-8");
?>
<html>
<body>
<form method='get'>
Номера: <input type=text size=3 name='prefix'> <input type=text size=7 name='start'> <input type=submit value="получить PDF">
<p>
Страниц: <input type=text size=3 name='pages' value=1>
<p>
Повторов номера: <input type=text size=3 name='mode' value=5>
</form>
<p>

<!--
<table border=0>
<tr><td height="2000">&nbsp;</td></tr>
<table>
<form method='post'>
<input type=hidden name='src' value='666'>
<input type=submit value="НЕ НАЖИМАТЬ!">
</form>
-->
</body>
</html>
<?php
} else {

  $p = PDF_new();
  pdf_set_parameter($p, "SearchPath", "./");

  $pw = 21 / 2.54 * 72;
  $ph = 29.7 / 2.54 * 72;

  PDF_begin_document($p, "", "");

  for($page=0;$page<$pages;$page++) {
    PDF_begin_page_ext($p, $pw, $ph, "");
    $pp = $page + 1;
    PDF_create_bookmark($p, "Page $pp", "");

    pdf_set_parameter($p, "FontOutline", "bar=fre3of9x.ttf");
    pdf_set_parameter($p, "FontOutline", "cour=courbd.ttf");
    $bcf = PDF_findfont($p, "bar", "winansi", 1);

    #$font1 = PDF_load_font($p, "Times-Bold", "winansi", "");
    $font1 = PDF_load_font($p, "cour", "winansi", "");

    $top = 0.4/2.54*72;
    $left = 1.0/2.54*72;

    $sw = 3.74*5 /2.54*72;
    $sh = 1.75*5 / 2.54 * 72;

    $cells_w = 1;
    $cells_h = 3;

    PDF_setlinewidth($p, 0.5);
    for($i=0;$i<=$cells_w;$i++) {
      PDF_moveto($p, $i * $sw + $left, $ph - $top);
      PDF_lineto($p, $i * $sw + $left, $ph - ($top + $sh * $cells_h));
    }
    for($i=0;$i<=$cells_h;$i++) {
      PDF_moveto($p, $left, $ph - ($top + $i * $sh));
      PDF_lineto($p, $left + $cells_w * $sw, $ph - ($top + $i * $sh));
    }

    PDF_closepath_stroke($p);

    $sw /= 5;
    $sh /= 5;
    $cells_w = 5;
    $cells_h = 5*3;
    $xo = 0.7/2.54*72;
    $yo = 1.0/2.54*72;
    $count = 0;

    #PDF_set_parameter($p, "underline", "true");
    for($y=0;$y<$cells_h;$y++) {
      for($x=0;$x<$cells_w;$x++) {
        $msg = sprintf("%s %06d", $prefix, $start);
	PDF_setfont($p, $font1, 15);
	PDF_show_xy($p, "".$msg."", $left + $xo + $x * $sw, $ph - ($top + $y * $sh + $yo) - 4);
	PDF_setfont($p, $bcf, 19);
	PDF_show_xy($p, "*".$msg."*", $left + $xo + $x * $sw, $ph - ($top + $y * $sh + $yo) + 8);
	$count++;
	if($count == $mode) {
          $start++;
	  $count=0;
	}
      }
    }


    PDF_end_page_ext($p, "");
  }

  PDF_end_document($p, "");

  $buf = PDF_get_buffer($p);
  $len = strlen($buf);
  header("Content-type: application/pdf");
  header("Content-Length: $len");
  header("Content-Disposition: inline; filename=numbers.pdf");
  print $buf;
  PDF_delete($p);
}
?>
