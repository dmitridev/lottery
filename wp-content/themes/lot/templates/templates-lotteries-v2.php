<?php
/*
Template Name: Лотереи V2
*/

?>
<!DOCTYPE html>

<head>
    <title>Таблица для проверки</title>
    <link rel="stylesheet" href="/wp-content/themes/lot/style.css">
</head>
</head>
<body>

    <div class="tech-lottery-table">
        <button class="hide_or_extend_on_left" onclick="toggleLeftTable()">&lt;</button>
        <h2>Проверка лотерей</h2>
        <select class="lotteries-select">
            <option value="">Выберите таблицу</option>
        </select>

    </div>
    <div class="table-place">

    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function (e) {
            (async () => {
                const formData = new FormData();
                formData.append('action', 'get_lotteries_names');
                const request = await fetch('/wp-admin/admin-ajax.php/', {
                    method: 'post',
                    body: formData,
                });
                const json = await request.json();

                Object.entries(json).forEach(([k, v], index) => {
                    const option = document.createElement('option');
                    option.value = v;
                    option.innerHTML = k;
                    
                    document.querySelector('.lotteries-select').appendChild(option);
                })

                const loadTable = async function (link) {
                    const html = await fetch(link);
                    return await html.text();
                }

                async function changeValue($this) {
                    const name = $this.value;
                    const text = await loadTable(name);
                    const div = document.createElement('div');
                    div.innerHTML = text;
                    document.querySelector('.table-place').innerHTML = div.innerHTML;
                    eval(document.querySelector('script').innerHTML);
                }

                document.querySelector('.lotteries-select').addEventListener('change', async function (e) {
                    await changeValue(this);
                });
            })()
        });

        function toggleLeftTable(){
            const table = document.querySelector('.tech-lottery-table');
            if(table.classList.contains('hide')){
                document.querySelector('.hide_or_extend_on_left').innerHTML = "<";
            } else {
                document.querySelector('.hide_or_extend_on_left').innerHTML = ">";
            }

            table.classList.toggle("hide");
        }

        window.toggleLeftTable = toggleLeftTable;
    </script>
</body>

</html>