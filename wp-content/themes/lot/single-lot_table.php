<html>
<head>
    <title>Таблица для проверки</title>
    <link rel="stylesheet" href="/wp-content/themes/lot/style.css">
</head>
<body>
<?php 
   // get_header();

   
?>

<?php 
    the_title();
?>

   <section class="lottery" style="margin-top:100px">
        <div class="container">
            <table class="lottery-table" cellspacing="0" cellpadding="0">
                <thead>
                    <tr>
                        <th rowspan="2" >
                        №
                        </th>
                        <th rowspan="2">Проверка данных</th>
                        
                        <th colspan="2" style="text-align:center">Да</th>
                        <th colspan="2" style="text-align:center">Нет</th>
                    </tr>
                    <tr>
                        
                        <th>Макс</th>
                        <th>Сейчас</th>
                        <th>Макс</th>
                        <th>Сейчас</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($res as $index => $row) { ?>
                        <?php
                        $diff_yes = abs($row['VALUE_YES'] - $row['VALUE_YES_NOW']);
                        $diff_no = abs($row['VALUE_NO'] - $row['VALUE_NO_NOW']);
                        // разница между значением "сейчас" и "макс" от 0 до 6 подсвечивается разными цветами.
                        $array_of_styles = array(
                            'color:#c00',
                            'background:#fe7c00',
                            'background:#ffc000',
                            'background:#fbea79',
                            'background:#d8e4bc',
                            'background:#8db4e2',
                            'background:#ccc0da',
                        );
                        ?>
                        <tr id="<?= $row['VALUE_ID'] ?>">
                            <td data-name="INDEX">
                                <?= $index + 1 ?>
                            </td>
                            <th class="<?php if (str_starts_with($row['VALUE_ID'], 'NUM_'))
                                echo "blue-header";
                            else
                                echo "green-header"; ?>
                                    "><?= $row['VALUE_CONDITION'] ?>
                            </th>
                            <td class="coefficient">
                                <?= $row['FIRST_COEFFICIENT'] ?>
                            </td>
                            <? // нужно выбрать цвет для этой штуки. ?>
                            <td <?php if ($diff_yes == 0)
                                echo 'style="' . $array_of_styles[0] . '"' ?> data-name="VALUE_YES">
                                <?= $row['VALUE_YES'] ?></td>
                            <td <?php if ($diff_yes <= 6)
                                echo 'style="' . $array_of_styles[$diff_yes] . '"' ?>
                                    data-name="VALUE_YES_NOW"><?= $row['VALUE_YES_NOW'] ?></td>
                            <td <?php if ($diff_no == 0)
                                echo 'style="' . $array_of_styles[0] . '"' ?> data-name="VALUE_NO">
                                <?= $row['VALUE_NO'] ?></td>
                            <td <?php if ($diff_yes <= 6)
                                echo 'style="' . $array_of_styles[$diff_no] . '"' ?>data-name="VALUE_NO_NOW"><?= $row['VALUE_NO_NOW'] ?>
                            </td>
                            <td class="coefficient">
                                <?= isset($row['SECOND_COEFFICIENT']) ? $row['SECOND_COEFFICIENT'] : $row['SECOND_COEFFICIENT'] ?>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </section>

<?php 
   // get_footer();
?>

</body>
</html>