<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>PDF Created</title>

    <style type="text/css">

        body {
            background-color: #fff;
            margin: 40px;
            font-family: Lucida Grande, Verdana, Sans-serif;
            font-size: 14px;
            color: #4F5155;
        }

        a {
            color: #003399;
            background-color: transparent;
            font-weight: normal;
        }

        h1 {
            border-bottom: 1px solid #D0D0D0;
            font-size: 16px;
        }

        h2 {
            font-size: 14px;
        }

        h1, h2 {
            color: #444;
            background-color: transparent;
            font-weight: bold;
            margin: 5px 0 5px 0;
            padding: 5px 0 6px 0;
        }

        .code {
            font-family: Monaco, Verdana, Sans-serif;
            background-color: #f9f9f9;
            border: 1px solid #D0D0D0;
            display: block;
            margin: 14px 0 14px 0;
            padding: 12px 10px 12px 10px;
        }

        p {
            color: #444;
            background-color: transparent;
            font-size: 14px;
            margin: 10px;
            padding: 10px;
        }

        li {
            color: #444;
            background-color: transparent;
            font-size: 14px;
            margin: 5px;
            padding: 5px;
        }

    </style>
</head>
<body>

<h1><a href="www.atuhotel.com">aTUhotel.com</a></h1>

<h1><?= $title; ?></h1>

<?php foreach ($reserves as $key => $item): ?>
    <div class="code">

        <h2>
            Hotel <?php echo $item["hotel_name"]; ?>

            <?php

            $dateInitial_i18n = date($lang == "en" ? "Y-m-d" : "d-m-Y", strtotime($item['start_date']));
            $dateEnd_i18n = date($lang == "en" ? "Y-m-d" : "d-m-Y", strtotime($item['end_date']));

            echo ($lang == 'en' ? ' (' . $dateInitial_i18n . ' --- ' . $dateEnd_i18n : ' (' . $dateInitial_i18n . ' --- ' . $dateEnd_i18n) . ')'; ?>

        </h2>

        <span>
            <?php echo(($lang == 'en' ? 'Status Reservation: ' : 'Situaci&oacute;n Reservaci&oacute;n: ') . $item["status"]); ?>
        </span>

        <span>
            <?php echo ($lang == 'en' ? 'Price: ' : 'Precio: ') . $item["price"] . '€'; ?>
        </span>

        <h2>
            <?php echo
                  ($lang == 'en' ? ' ' . $item['listing_types_name_en'] : $item['listing_types_name_es']) . " "
                . ($lang == 'en' ? 'Country: ' . $item['countries_name_en'] : 'Pa&iacute;s: ' . $item['countries_name_es'])
                . ($lang == 'en' ? ', City: ' : ', Ciudad: ') . $item['city']; ?>

    </div>
<?php endforeach; ?>

<h1><a href="www.atuhotel.com">aTUhotel.com</a></h1>

</body>
</html>