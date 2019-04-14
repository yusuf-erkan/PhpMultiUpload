<?php
  function multiUpload($dosyalar)
  {
    $sonuc=[];
    //Hata kontrolü
    foreach ($dosyalar['error'] as $index => $error) {
      //dosya seçilmediği zaman error değeri 4 oluyor.
      if ($error==4) {
        $sonuc['hata'] = 'Lütfen dosya seçin!!';
      }elseif ($error != 0) {
        $sonuc['hata'][]='Dosya yüklenirken bir sorun oluştu #' . $dosyalar['name'][$index];
      }
    }
    //Hata yoksa işleme devam et
    if (!isset($sonuc['hata'])) {
      //Dosya uzantılarının kontrolü
      $gecerliDosyaUzantilari = [
        'image/jpeg'
        //'image/png'
      ];
      foreach ($dosyalar['type'] as $index => $type) {
        if (!in_array($type, $gecerliDosyaUzantilari)) {
          //Gelen type geçerli uzantı mı?
          $sonuc['hata'][] = 'Dosya geçersiz bir formatta #' . $dosyalar['name'][$index];
        }
      }

      if (!isset($sonuc['hata'])) {
        //Dosya boytunun kontrolü
        $maxBoyut = (1024*1024*3);//1Mb max boyut

        foreach ($dosyalar['size'] as $index => $size) {
          if ($size > $maxBoyut) {
            $sonuc['hata'][] = 'Dosya boyutu fazla #' . $dosyalar['name'][$index];
          }
        }

        if (!isset($sonuc['hata'])) {
          //Hata yoksa dosyaların yüklenmesi
          foreach ($dosyalar['tmp_name'] as $index => $tmp) {
            $dosyaAdi = $dosyalar['name'][$index];
            $yukle = move_uploaded_file($tmp, 'upload/' .$dosyaAdi);
            if ($yukle) {
              $sonuc['dosya'][] = 'upload/' . $dosyaAdi;
            }else {
              $sonuc['hata'] = 'Dosya yuklenemedi! #' . $dosyaAdi;
            }
          }
        }
      }
    }
    return $sonuc;
  }
  $sonuc = multiUpload($_FILES["dosya"]);

  if (isset($sonuc['dosya'])) {
    print_r($sonuc['dosya']);
    if (isset($sonuc['hata'])) {
      print_r($sonuc['hata']);
    }
  }elseif (isset($sonuc['hata'])) {
    if (is_array($sonuc['hata'])) {
      echo implode('<br>', $sonuc['hata']);
    }else {
      echo $sonuc['hata'];
    }
  }

?>
