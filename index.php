<?php
$url = "https://data.bmkg.go.id/DataMKG/MEWS/DigitalForecast/DigitalForecast-BangkaBelitung.xml";
$dataGet = simplexml_load_file($url) or die("Gagal mengakses!");
$dataGet = $dataGet->forecast->area;
$data = [];

$cuacaName = [
    "0" => "Cerah",
    "1" => "Cerah Berawan",
    "2" => "Cerah Berawan",
    "3" => "Berawan",
    "4" => "Berawan Tebal",
    "5" => "Udara Kabur",
    "10" => "Asap",
    "45" => "Kabut",
    "60" => "Hujan Ringan",
    "61" => "Hujan Sedang",
    "63" => "Hujan Lebat",
    "80" => "Hujan Lokal",
    "95" => "Hujan Petir",
    "97" => "Hujan Petir",
];

$cuacaImage = [
    "5" => "Udara Kabur", //
    "10" => "Asap", //
    "80" => "Hujan Lokal", //
];

$arah = [
    "N" => "Utara",
    "NNE" => "Utara - timur laut",
    "NE" => "Timur laut",
    "ENE" => "Timur-Timur Laut",
    "E" => "Timur",
    "ESE" => "Timur-Tenggara",
    "SE" => "Tenggara",
    "SSE" => "Tenggara-Selatan",
    "S" => "Selatan",
    "SSW" => "Selatan-Barat Daya",
    "SW" => "Barat daya",
    "WSW" => "Barat-Barat Daya",
    "W" => "Barat",
    "WNW" => "Barat-Barat Laut",
    "NW" => "Barat laut",
    "NNW" => "Utara-Barat Laut",
    "VARIABLE" => "berubah-ubah",
];
date_default_timezone_set('Asia/Jakarta');
foreach ($dataGet as $val) {
    $kelembabab = $val->parameter[1];
    $suhu = $val->parameter[5];
    $cuaca = $val->parameter[6];
    $arahAngin = $val->parameter[7];
    $kecepatan = $val->parameter[8];

    if ($kelembabab) $kelembabab = $kelembabab->timerange;
    if ($suhu) $suhu = $suhu->timerange;
    if ($arahAngin) $arahAngin = $arahAngin->timerange;
    if ($kecepatan) $kecepatan = $kecepatan->timerange;

    if ($cuaca) {
        $x = [];
        $i = 0;
        foreach ($cuaca->timerange as $value) {
            $time = date('H:i', strtotime($value->attributes()->datetime));
            $timeNow = date('H:i', strtotime(date('H:i')." - 2 hour"));
            if ($time < $timeNow) {
                $sVal = $suhu[$i]->value[0];
                $x[] = [
                    "date" => date('l, d F Y H:i:s', strtotime($value->attributes()->datetime))." WIB",
                    "time" => $time." WIB",
                    "cuaca" => $cuacaName[(int)$value->value],
                    "suhu" => (int)$sVal."° ".$sVal->attributes()->unit,
                    "pmAm" => $time >= '18:00' || $time < '06:00' ? 'malam' : 'siang',
                    "kelembabab" => (int)$kelembabab->value." %",
                    "arahAngin" => $arah[(string)$arahAngin->value[1]],
                    "simbolArah" => (string)$arahAngin->value[1],
                    "kecepatan" => (int)$kecepatan->value[0]." km/jam",
                ];
                $i++;
            }
        }
        $x = [$x[count($x)-1]];
        $data[] = [
            "namaKota" => $val->name[1],
            "provinsi" => $val->attributes()->domain,
            "data" => $x
        ];
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cuaca</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="assets/owlcarousel/owl.carousel.min.css">
    <link rel="stylesheet" href="assets/owlcarousel/owl.theme.default.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css?family=Montserrat:400,700,900&display=swap');
        body {
            margin: 0;
            width: 100%;
            height: 100vh;
            font-family: 'Montserrat', sans-serif !important;
            background-color: #343d4b;
            display: -webkit-box;
            display: -ms-flexbox;
            display: flex;
            -webkit-box-align: center;
                -ms-flex-align: center;
                    align-items: center;
            -webkit-box-pack: center;
                -ms-flex-pack: center;
                    justify-content: center;
        }
        .card{
            border:0;
        }
        .cuaca-area{
            padding: 10px;
            text-align: center;
            background-repeat: no-repeat;
            background-position: center center;
            background-size: cover;
            height: 100%;
            width: 100%;
            position: relative;
            color: #fff;
            border:0;
        }
        .text-cuaca{
            font-size: 16px;
            font-weight: 400;
        }
        .text-cuaca-info{
            font-size: 14px;
            margin-top: 5px;
            font-weight: 400;
        }
        .text-suhu{
            font-size: 24px;
            line-height: 24px;
            font-weight: 400;
        }
        .cuaca-area img{
            margin-top: 4px;
            margin-bottom: 15px;
            width: 64px !important;
            height: 64px !important;
            text-align: center;
            filter: drop-shadow(0px 0px 10px #fff);
            -webkit-filter: drop-shadow(0px 0px 10px #fff);
        }

        .cuaca-area.bg-malam{
            background-image: url('assets/malam.jpg');
        }

        .cuaca-area.bg-siang{
            background-image: url('assets/siang.jpg');
        }

        .simbol-arah.N{
            transform: rotate(135deg);
        }

        .simbol-arah.NE{
            transform: rotate(180deg);
        }

        .simbol-arah.E{
            transform: rotate(225deg);
        }

        .simbol-arah.SE{
            transform: rotate(270deg);
        }

        .simbol-arah.S{
            transform: rotate(315deg);
        }

        .simbol-arah.SW{
            transform: rotate(0);
        }

        .simbol-arah.W{
            transform: rotate(45deg);
        }

        .simbol-arah.NW{
            transform: rotate(90deg);
        }

        .simbol-arah.NNE{
            transform: rotate(225deg);
        }
        .simbol-arah.ESE{
            transform: rotate(135deg);
        }
        

        .simbol-arah.SSE{
            transform: rotate(135deg);
        }
        
        .simbol-arah.SSW{
            transform: rotate(135deg);
        }
        
        .simbol-arah.WSW{
            transform: rotate(135deg);
        }
        
        .simbol-arah.WNW{
            transform: rotate(135deg);
        }
        
        .simbol-arah.NNW{
            transform: rotate(135deg);
        }

        .owl-carousel .owl-nav{
            position: absolute;
            width: 100%;
            top: 36%;
            display: flex;
            justify-content: space-between;
            padding: 10px;
        }
        .owl-carousel .owl-nav button.owl-next,
        .owl-carousel .owl-nav button.owl-prev,
        .owl-carousel button.owl-dot{
            color: #fff;
            font-size: xxx-large;
        }
    </style>
</head>
<body>
    <div class="container m-3">
        <div class="owl-carousel">
            <?php foreach ($data as $i => $val) { ?>
                <?php foreach ($val['data'] as $ii => $value) { ?>
                    <div class="card">
                        <div class="card-body d-flex flex-column align-items-center cuaca-area bg-<?=$value['pmAm']?>">
                            <span class="text-cuaca"><?=$val['namaKota']?></span>
                            <span class="text-cuaca my-2"><?=$value['time']?></span>
                            <img src="assets/<?=$value['cuaca']?>-<?=$value['pmAm']?>.png">
                            <span class="text-cuaca-info my-2"><?=$value['cuaca']?></span>
                            <span class="text-suhu mb-2"><i class="fa-solid fa-temperature-half"></i> <?=$value['suhu']?></span>
                            <span class="text-cuaca-info mb-2"><i class="fa-solid fa-droplet"></i> <?=$value['kelembabab']?></span>
                            <span class="text-cuaca-info mb-2"><i class="fa-solid fa-wind"></i> <?=$value['kecepatan']?></span>
                            <span class="text-cuaca-info mb-2"><i class="fa-solid fa-location-arrow simbol-arah <?=$value['simbolArah']?>"></i> <?=$value['arahAngin']?></span>
                        </div>
                    </div>
                <?php } 
                }; ?>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js" integrity="sha512-yFjZbTYRCJodnuyGlsKamNE/LlEaEAxSUDe5+u61mV8zzqJVFOH7TnULE2/PP/l5vKWpUNnF4VGVkXh3MjgLsg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/owlcarousel/owl.carousel.min.js"></script>
    <script>
        $(document).ready(function(){
            var owl = $('.owl-carousel');
            owl.owlCarousel({
                items:4,
                loop:true,
                margin:10,
                autoplay:true,
                autoplayTimeout:3000,
                autoplayHoverPause:true,
                nav:true,
            });
        });
    </script>
</body>
</html>