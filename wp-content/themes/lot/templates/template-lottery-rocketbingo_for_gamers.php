<html>

<head>
    <title>Таблица для проверки</title>
    <link rel="stylesheet" href="/wp-content/themes/lot/style.css">
</head>
<?php
/*
Template Name: Лотерея rocketbingo_for_gamers
*/
//get_header();

global $wpdb;

$res = $wpdb->get_results("SELECT * FROM `wp_lottery_results` where `LOTO_TYPE`='rocketbingo_for_gamers'", ARRAY_A);
?>
<style>
    input[data-name^="NUMBER"] {
        width: 50px;
        margin-right: 10px;
    }

    td[data-name="ID"] {
        width: 72px;
    }

    .table-row {
        display: flex;
        flex-flow: row wrap;
    }
</style>

<main class="for-participants пав">
    <section class="header">
        <div class="container">
            <div class="breadcrumbs">
                <a href="/">Главная</a> / <a href="#">Информация для участников</a>
            </div>
            <h1>ЛОТЕРЕИ</h1>
        </div>
    </section>
    <?php
    $update = $wpdb->get_var("SELECT LAST_UPDATE from `wp_lottery_update` where LOTTERY_NAME='rocketbingo_for_gamers'");
    $table = $wpdb->get_results("SELECT * from `wp_lottery_rocketbingo_for_gamers`", ARRAY_A);
    ?>
    <!--Так нужно для динамической подгрузки этой страницы-->

    <script>
        
		async function add_new(){
			const tr = document.createElement('tr');
            	tr.classList.add('table-row');
            	tr.innerHTML = `<td data-name="ID"></td>
                        <td><input data-name="NUMBER1" value=""></td>
	<td><input data-name="NUMBER2" value=""></td>
	<td><input data-name="NUMBER3" value=""></td>
	<td><input data-name="NUMBER4" value=""></td>
	<td><input data-name="NUMBER5" value=""></td>
	<td><input data-name="NUMBER6" value=""></td>
	<td><input data-name="NUMBER7" value=""></td>
	<td><input data-name="NUMBER8" value=""></td>
	<td><input data-name="NUMBER9" value=""></td>
	<td><input data-name="NUMBER10" value=""></td>
	<td><input data-name="NUMBER11" value=""></td>
	<td><input data-name="NUMBER12" value=""></td>
	<td><input data-name="NUMBER13" value=""></td>
	<td><input data-name="NUMBER14" value=""></td>
	<td><input data-name="NUMBER15" value=""></td>
	<td><input data-name="NUMBER16" value=""></td>
	<td><input data-name="NUMBER17" value=""></td>
	<td><input data-name="NUMBER18" value=""></td>
	<td><input data-name="NUMBER19" value=""></td>
	<td><input data-name="NUMBER20" value=""></td>
	<td><input data-name="NUMBER21" value=""></td>
	<td><input data-name="NUMBER22" value=""></td>
	<td><input data-name="NUMBER23" value=""></td>
	<td><input data-name="NUMBER24" value=""></td>
	<td><input data-name="NUMBER25" value=""></td>
	<td><input data-name="NUMBER26" value=""></td>
	<td><input data-name="NUMBER27" value=""></td>
	<td><input data-name="NUMBER28" value=""></td>
	<td><input data-name="NUMBER29" value=""></td>
	<td><input data-name="NUMBER30" value=""></td>
	<td><input data-name="NUMBER31" value=""></td>
	<td><input data-name="NUMBER32" value=""></td>
	<td><input data-name="NUMBER33" value=""></td>
	<td><input data-name="NUMBER34" value=""></td>
	<td><input data-name="NUMBER35" value=""></td>
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
                const NUMBER1 = !!tr.querySelector('[data-name="NUMBER1"]').value ? tr.querySelector('[data-name="NUMBER1"]').value:0;
	const NUMBER2 = !!tr.querySelector('[data-name="NUMBER2"]').value ? tr.querySelector('[data-name="NUMBER2"]').value:0;
	const NUMBER3 = !!tr.querySelector('[data-name="NUMBER3"]').value ? tr.querySelector('[data-name="NUMBER3"]').value:0;
	const NUMBER4 = !!tr.querySelector('[data-name="NUMBER4"]').value ? tr.querySelector('[data-name="NUMBER4"]').value:0;
	const NUMBER5 = !!tr.querySelector('[data-name="NUMBER5"]').value ? tr.querySelector('[data-name="NUMBER5"]').value:0;
	const NUMBER6 = !!tr.querySelector('[data-name="NUMBER6"]').value ? tr.querySelector('[data-name="NUMBER6"]').value:0;
	const NUMBER7 = !!tr.querySelector('[data-name="NUMBER7"]').value ? tr.querySelector('[data-name="NUMBER7"]').value:0;
	const NUMBER8 = !!tr.querySelector('[data-name="NUMBER8"]').value ? tr.querySelector('[data-name="NUMBER8"]').value:0;
	const NUMBER9 = !!tr.querySelector('[data-name="NUMBER9"]').value ? tr.querySelector('[data-name="NUMBER9"]').value:0;
	const NUMBER10 = !!tr.querySelector('[data-name="NUMBER10"]').value ? tr.querySelector('[data-name="NUMBER10"]').value:0;
	const NUMBER11 = !!tr.querySelector('[data-name="NUMBER11"]').value ? tr.querySelector('[data-name="NUMBER11"]').value:0;
	const NUMBER12 = !!tr.querySelector('[data-name="NUMBER12"]').value ? tr.querySelector('[data-name="NUMBER12"]').value:0;
	const NUMBER13 = !!tr.querySelector('[data-name="NUMBER13"]').value ? tr.querySelector('[data-name="NUMBER13"]').value:0;
	const NUMBER14 = !!tr.querySelector('[data-name="NUMBER14"]').value ? tr.querySelector('[data-name="NUMBER14"]').value:0;
	const NUMBER15 = !!tr.querySelector('[data-name="NUMBER15"]').value ? tr.querySelector('[data-name="NUMBER15"]').value:0;
	const NUMBER16 = !!tr.querySelector('[data-name="NUMBER16"]').value ? tr.querySelector('[data-name="NUMBER16"]').value:0;
	const NUMBER17 = !!tr.querySelector('[data-name="NUMBER17"]').value ? tr.querySelector('[data-name="NUMBER17"]').value:0;
	const NUMBER18 = !!tr.querySelector('[data-name="NUMBER18"]').value ? tr.querySelector('[data-name="NUMBER18"]').value:0;
	const NUMBER19 = !!tr.querySelector('[data-name="NUMBER19"]').value ? tr.querySelector('[data-name="NUMBER19"]').value:0;
	const NUMBER20 = !!tr.querySelector('[data-name="NUMBER20"]').value ? tr.querySelector('[data-name="NUMBER20"]').value:0;
	const NUMBER21 = !!tr.querySelector('[data-name="NUMBER21"]').value ? tr.querySelector('[data-name="NUMBER21"]').value:0;
	const NUMBER22 = !!tr.querySelector('[data-name="NUMBER22"]').value ? tr.querySelector('[data-name="NUMBER22"]').value:0;
	const NUMBER23 = !!tr.querySelector('[data-name="NUMBER23"]').value ? tr.querySelector('[data-name="NUMBER23"]').value:0;
	const NUMBER24 = !!tr.querySelector('[data-name="NUMBER24"]').value ? tr.querySelector('[data-name="NUMBER24"]').value:0;
	const NUMBER25 = !!tr.querySelector('[data-name="NUMBER25"]').value ? tr.querySelector('[data-name="NUMBER25"]').value:0;
	const NUMBER26 = !!tr.querySelector('[data-name="NUMBER26"]').value ? tr.querySelector('[data-name="NUMBER26"]').value:0;
	const NUMBER27 = !!tr.querySelector('[data-name="NUMBER27"]').value ? tr.querySelector('[data-name="NUMBER27"]').value:0;
	const NUMBER28 = !!tr.querySelector('[data-name="NUMBER28"]').value ? tr.querySelector('[data-name="NUMBER28"]').value:0;
	const NUMBER29 = !!tr.querySelector('[data-name="NUMBER29"]').value ? tr.querySelector('[data-name="NUMBER29"]').value:0;
	const NUMBER30 = !!tr.querySelector('[data-name="NUMBER30"]').value ? tr.querySelector('[data-name="NUMBER30"]').value:0;
	const NUMBER31 = !!tr.querySelector('[data-name="NUMBER31"]').value ? tr.querySelector('[data-name="NUMBER31"]').value:0;
	const NUMBER32 = !!tr.querySelector('[data-name="NUMBER32"]').value ? tr.querySelector('[data-name="NUMBER32"]').value:0;
	const NUMBER33 = !!tr.querySelector('[data-name="NUMBER33"]').value ? tr.querySelector('[data-name="NUMBER33"]').value:0;
	const NUMBER34 = !!tr.querySelector('[data-name="NUMBER34"]').value ? tr.querySelector('[data-name="NUMBER34"]').value:0;
	const NUMBER35 = !!tr.querySelector('[data-name="NUMBER35"]').value ? tr.querySelector('[data-name="NUMBER35"]').value:0;
                const CURRENT = index;
                array.push({
                    numbers: [NUMBER1,NUMBER2,NUMBER3,NUMBER4,NUMBER5,NUMBER6,NUMBER7,NUMBER8,NUMBER9,NUMBER10,NUMBER11,NUMBER12,NUMBER13,NUMBER14,NUMBER15,NUMBER16,NUMBER17,NUMBER18,NUMBER19,NUMBER20,NUMBER21,NUMBER22,NUMBER23,NUMBER24,NUMBER25,NUMBER26,NUMBER27,NUMBER28,NUMBER29,NUMBER30,NUMBER31,NUMBER32,NUMBER33,NUMBER34,NUMBER35],
                    number: CURRENT
                });
            });

            const formData = new FormData();

            formData.append('action', 'insert_rocketbingo_for_gamers');
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

        async function update_table($this) {
            let text = $this.innerHTML;
            $this.innerHTML = "Загрузка...";
            $this.disabled = true;
            const formData = new FormData();
            formData.append("action", 'get_rocketbingo_for_gamers');

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

            const value_yes_now = block.querySelector(`[data-name="VALUE_YES_NOW"]`);
            value_yes_now.innerHTML = v.VALUE_YES_NOW;
            if(diff_yes <=6)
                value_yes_now.style=array_of_styles[diff_yes];
            
            if(diff_no <= 6)
                value_no_now.style=array_of_styles[diff_no];
            const value_no_now = block.querySelector(`[data-name="VALUE_NO_NOW"]`);
            value_no_now.innerHTML = v.VALUE_NO_NOW;


        });

        document.querySelector(".update-time").innerHTML = table.update;
        $this.innerHTML = text;
        $this.disabled = false;
    }

        
        window.add_new = add_new;
        window.update_data = update_data;
        window.delete_data = delete_data;
        window.update_table = update_table;

    </script>

    <section class="lottery" style="position:fixed;top:0;width:100%;background:white">
        <div class="container" style="height:210px;overflow:scroll">
            <table class="lottery-table edit" cellspacing="0" cellpadding="0">
                <tbody>
                    <?php foreach ($table as $row) { ?>
                        <tr class="table-row">
                            <td data-name="ID">
                                <?= $row['ID'] ?>
                            </td>
                            <td><input data-name="NUMBER1" value="<?= $row['NUMBER1'] ?>"></td>
	<td><input data-name="NUMBER2" value="<?= $row['NUMBER2'] ?>"></td>
	<td><input data-name="NUMBER3" value="<?= $row['NUMBER3'] ?>"></td>
	<td><input data-name="NUMBER4" value="<?= $row['NUMBER4'] ?>"></td>
	<td><input data-name="NUMBER5" value="<?= $row['NUMBER5'] ?>"></td>
	<td><input data-name="NUMBER6" value="<?= $row['NUMBER6'] ?>"></td>
	<td><input data-name="NUMBER7" value="<?= $row['NUMBER7'] ?>"></td>
	<td><input data-name="NUMBER8" value="<?= $row['NUMBER8'] ?>"></td>
	<td><input data-name="NUMBER9" value="<?= $row['NUMBER9'] ?>"></td>
	<td><input data-name="NUMBER10" value="<?= $row['NUMBER10'] ?>"></td>
	<td><input data-name="NUMBER11" value="<?= $row['NUMBER11'] ?>"></td>
	<td><input data-name="NUMBER12" value="<?= $row['NUMBER12'] ?>"></td>
	<td><input data-name="NUMBER13" value="<?= $row['NUMBER13'] ?>"></td>
	<td><input data-name="NUMBER14" value="<?= $row['NUMBER14'] ?>"></td>
	<td><input data-name="NUMBER15" value="<?= $row['NUMBER15'] ?>"></td>
	<td><input data-name="NUMBER16" value="<?= $row['NUMBER16'] ?>"></td>
	<td><input data-name="NUMBER17" value="<?= $row['NUMBER17'] ?>"></td>
	<td><input data-name="NUMBER18" value="<?= $row['NUMBER18'] ?>"></td>
	<td><input data-name="NUMBER19" value="<?= $row['NUMBER19'] ?>"></td>
	<td><input data-name="NUMBER20" value="<?= $row['NUMBER20'] ?>"></td>
	<td><input data-name="NUMBER21" value="<?= $row['NUMBER21'] ?>"></td>
	<td><input data-name="NUMBER22" value="<?= $row['NUMBER22'] ?>"></td>
	<td><input data-name="NUMBER23" value="<?= $row['NUMBER23'] ?>"></td>
	<td><input data-name="NUMBER24" value="<?= $row['NUMBER24'] ?>"></td>
	<td><input data-name="NUMBER25" value="<?= $row['NUMBER25'] ?>"></td>
	<td><input data-name="NUMBER26" value="<?= $row['NUMBER26'] ?>"></td>
	<td><input data-name="NUMBER27" value="<?= $row['NUMBER27'] ?>"></td>
	<td><input data-name="NUMBER28" value="<?= $row['NUMBER28'] ?>"></td>
	<td><input data-name="NUMBER29" value="<?= $row['NUMBER29'] ?>"></td>
	<td><input data-name="NUMBER30" value="<?= $row['NUMBER30'] ?>"></td>
	<td><input data-name="NUMBER31" value="<?= $row['NUMBER31'] ?>"></td>
	<td><input data-name="NUMBER32" value="<?= $row['NUMBER32'] ?>"></td>
	<td><input data-name="NUMBER33" value="<?= $row['NUMBER33'] ?>"></td>
	<td><input data-name="NUMBER34" value="<?= $row['NUMBER34'] ?>"></td>
	<td><input data-name="NUMBER35" value="<?= $row['NUMBER35'] ?>"></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
            <button class="add_new" onclick="add_new()"> + Добавить данные </button>
            <button class="load_data" onclick="update_data()"> Загрузить список </button>
            <button class="update_data" onclick=update_table(this)> Обновить данные </button>
            <span>Дата последнего обновления: <span class="update-time">
                    <?= $update ?>
                </span></span>
        </div>
    </section>
    
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
    
</script>
<?php
get_footer();
?>