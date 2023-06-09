<html>

<head>
    <title>Таблица для проверки</title>
    <link rel="stylesheet" href="/wp-content/themes/lot/style.css">
</head>
<?php
/*
Template Name: Лотереи
*/

//get_header();

global $wpdb;

$res = $wpdb->get_results("SELECT * FROM `wp_lottery_results` where LOTTERY_NAME='stoloto'", ARRAY_A);
?>
<main class="for-participants">
    <section class="header">
        <div class="container">
            <div class="breadcrumbs">
                <a href="/">Главная</a> / <a href="#">Информация для участников</a>
            </div>
            <h1>ЛОТЕРЕИ</h1>
        </div>
    </section>

    <?php
    $update = $wpdb->get_var("SELECT LAST_UPDATE from `wp_lottery_update` where LOTTERY_NAME='stoloto'");
    $table = $wpdb->get_results("SELECT * from `wp_lottery` where LOTTERY_NAME='stoloto'", ARRAY_A);
    ?>

    <section class="lottery" style="position:fixed;top:0;width:100%;background:white">
        <div class="container" style="height:210px;overflow:scroll">
            <table class="lottery-table edit" cellspacing="0" cellpadding="0">
                <tbody>
                    <?php foreach ($table as $row) { ?>
                        <tr class="table-row">
                            <td data-name="ID">
                                <?= $row['ID'] ?>
                            </td>
                            <td>
                                <input data-name="NUMBER1" value="<?= $row['NUMBER1'] ?>">
                            </td>
                            <td>
                                <input data-name="NUMBER2" value="<?= $row['NUMBER2'] ?>">
                            </td>
                            <td>
                                <input data-name="NUMBER3" value="<?= $row['NUMBER2'] ?>">
                            </td>
                            <td>
                                <button class="delete_data" onclick="delete_data(this)">x</button>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
            <button class="add_new"> + Добавить данные </button>
            <button class="load_data" onclick="update_data()"> Загрузить список </button>
            <button class="update_data"> Обновить данные </button>
            <span>Дата последнего обновления: <span class="update-time">
                    <?= $update ?>
                </span></span>
        </div>
    </section>
    <script>
        document.querySelector('.add_new').onclick = function (e) {
            const tr = document.createElement('tr');
            tr.classList.add('table-row');
            tr.innerHTML = `<td data-name="ID"></td>
                        <td>
                            <input data-name="NUMBER1" value="">
                        </td>
                        <td>
                            <input data-name="NUMBER2" value="">
                        </td>
                        <td>
                            <input data-name="NUMBER3" value="">
                        </td>
                        <td>
                            <button class="delete_data" onclick="delete_data(this)">x</button>
                        </td>`;
            document.querySelector('.lottery-table.edit').appendChild(tr);
        }
        function delete_data(element) {
            const parent = element.parentNode.parentNode;
            parent.remove();
        }

        async function update_data() {
            let array = [];
            Array.from(document.querySelectorAll('.table-row')).forEach((tr, index) => {
                const NUMBER1 = tr.querySelector('[data-name="NUMBER1"]').value;
                const NUMBER2 = tr.querySelector('[data-name="NUMBER2"]').value;
                const NUMBER3 = tr.querySelector('[data-name="NUMBER3"]').value;
                const CURRENT = index;
                console.log(NUMBER1, NUMBER2, NUMBER3, CURRENT);
                array.push({
                    numbers: [NUMBER1, NUMBER2, NUMBER3],
                    number: CURRENT
                });
            });

            const formData = new FormData();

            formData.append('action', 'insert_stoloto');
            formData.append('rows', JSON.stringify(array));

            try {
                const request = await fetch('/wp-admin/admin-ajax.php/', {
                    method: 'post',
                    body: formData
                });
            } catch (e) {
                console.log(e);
            }
        }
    </script>

    <section class="lottery" style="margin-top:100px">
        <div class="container">
            <table class="lottery-table" cellspacing="0" cellpadding="0">
                <thead>
                    <tr>
                        <th rowspan="2" >
                        №
                        </th>
                        
                        <th rowspan="2">Проверка данных</th>
                        <th rowspan="2">Коэф</th>
                        
                        <th colspan="2" style="text-align:center">Да</th>
                        <th colspan="2" style="text-align:center">Нет</th>
                    
                        <th rowspan="2">Коэф</th>
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
</main>

<script>
    document.querySelector('.update_data').onclick = async function (e) {
        let text = this.innerHTML;
        this.innerHTML = "Загрузка...";
        this.disabled = true;
        const formData = new FormData();
        formData.append("action", 'get_stoloto_top');

        const request = await fetch('/wp-admin/admin-ajax.php/', {
            method: 'post',
            body: formData
        });
        const table = await request.json();

        Object.entries(table.results).forEach(([k, v], index) => {
            console.log(k);
            diff_yes = Math.abs(v.VALUE_YES_MAX - v.VALUE_YES_NOW);
            diff_no = Math.abs(v.VALUE_NO - v.VALUE_NO_NOW);
            // разница между значением "сейчас" и "макс" от 0 до 6 подсвечивается разными цветами.
            array_of_styles = [
                'color:#c00',
                'background:#fe7c00',
                'background:#ffc000',
                'background:#fbea79',
                'background:#d8e4bc',
                'background:#8db4e2',
                'background:#ccc0da',
            ];
            const block = document.getElementById(k);
            const index_block = block.querySelector(`[data-name="INDEX"]`);
            index_block.innerHTML = index + 1;
            
            const value_yes = block.querySelector(`[data-name="VALUE_YES"]`);
            if(diff_yes == 0)
                value_yes.style = array_of_styles[0];
            
            value_yes.innerHTML = v.VALUE_YES_MAX;

            const value_no = block.querySelector(`[data-name="VALUE_NO"]`);
            
            if(diff_no ==0)
                value_no.style = array_of_styles[0];
            value_no.innerHTML = v.VALUE_NO_MAX;

            const value_yes_now = block.querySelector(`[data-name="VALUE_YES_NOW"`);
            value_yes_now.innerHTML = v.VALUE_YES_NOW;
            if(diff_yes <=6)
                value_yes_now.style=array_of_styles[diff_yes];
            
            if(diff_no <= 6)
                value_no_now.style=array_of_styles[diff_no];
            const value_no_now = block.querySelector(`[data-name="VALUE_NO_NOW"]`);
            value_no_now.innerHTML = v.VALUE_NO_NOW;


        });

        document.querySelector(".update-time").innerHTML = table.update;
        this.innerHTML = text;
        this.disabled = false;
    }

</script>
<?php
get_footer();
?>