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

        table {
            width: 100%;
            border: 1px solid #D0D0D0;
        }

        td {
            width: 33%;
            border: 1px solid #D0D0D0;
        }

    </style>
</head>
<body>
<h1><a href="www.atuhotel.com">aTUhotel.com</a></h1>

<h1> <?= $owner; ?> </h1>

<?php for ($index = 0; $index < count($roomArray); $index++): ?>
    <div class="code">

        <p>
            <?php

            $reserves_temp = $reservesArray[$index];
            ?>
        </p>

        <?php if (count($reserves_temp) > 0): ; ?>


            <table>

                <tr style="font-weight: bold;">
                    <td>
                        <?php echo($lang == 'en' ? 'Room Name' : 'Nombre de la habitaci&oacute;n'); ?>
                    </td>
                    <td>
                        <?php echo($lang == 'en' ? 'Status Reservation' : 'Situaci&oacute;n Reservaci&oacute;n'); ?>
                    </td>
                    <td>
                        <?php echo($lang == 'en' ? 'Interval' : 'Intervalo'); ?>
                    </td>
                    <td>
                        <?php echo($lang == 'en' ? 'Client' : 'Cliente'); ?>
                    </td>
                    <td>
                        <?php echo($lang == 'en' ? 'Email' : 'Correo'); ?>
                    </td>
                    <td>
                        <?php echo($lang == 'en' ? 'Price' : 'Precio'); ?>
                    </td>
                </tr>


                <?php foreach ($reserves_temp as $item): ?>
                    <tr>
                        <td>
                            <?php echo ($lang == 'en' ? ' ' . $roomArray[$index]['categories_name_en'] : $roomArray[$index]['categories_name_es']) . " "
                                . ($lang == 'en' ? ' ' . $roomArray[$index]['listing_types_name_en'] : $roomArray[$index]['listing_types_name_es']) . " "
                            ?>

                        </td>
                        <td>
                            <?php echo $item['status']; ?>
                        </td>
                        <td>
                            <?php
                            $dateInitial_i18n = date($lang == "en" ? "Y-m-d" : "d-m-Y", strtotime($item['start_date']));
                            $dateEnd_i18n = date($lang == "en" ? "Y-m-d" : "d-m-Y", strtotime($item['end_date']));

                            echo 'from ' . $dateInitial_i18n . ' until ' . $dateEnd_i18n;
                            ?>
                        </td>
                        <td>
                            <?php echo $item["client_first_name"] . ' ' . $item["client_last_name"] ?>
                        </td>
                        <td>
                            <?php echo $item["client_email"] ?>
                        </td>
                        <td>
                            <?php echo $item["price"] . '€' ?>
                        </td>
                    </tr>


                <?php endforeach; ?>

            </table>


        <?php endif; ?>

    </div>

<?php endfor; ?>


<h1><a href="www.atuhotel.com">aTUhotel.com</a></h1>
</body>
</html>