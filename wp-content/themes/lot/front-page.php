<?php get_header(); ?>

<main class="main">
    <section class="first-slide">
        <div class="container slide first flex-block justify-center ">
            <img src="/wp-content/themes/lot/assets/images/slide-1-background.svg" class="background">
            <div class="wrapper flex-block">
                <h1 class="heading">
                    АКТУАЛЬНАЯ, КОМПЛЕКСНАЯ И ДЕТАЛЬНАЯ<br> ИНФОРМАЦИЯ ИГРОКАМ,
                    ДЕЛАЮЩИМ СТАВКИ <br>В БУКМЕКЕРСКИХ КОНТОРАХ, УЧАСТНИКАМ<br> "НАЦИОНАЛЬНОЙ ЛОТЕРЕИ" И "СТОЛОТО"
                </h1>
                <h1 class="number">18+</h1>
            </div>
        </div>
    </section>
    <section class="second-slide">
        <div class="container slide second">
            <div class="row">
                <div class="col-6 card">
                    <div class="text flex-block align-center justify-center">
                        <div class="information">
                            <img src="/wp-content/themes/lot/assets/images/lottery-yellow.svg" alt="">
                            <div class="block">
                                <h2>ДЛЯ ИГРОКОВ</h2>
                                <p>Игрок — рациональный индивид, который принимает решения, сравнивая возможные
                                    выгоды и
                                    издержки, стремясь увеличить своё благосостояние</p>
                            </div>
                        </div>
                        <a class="button yellow flex-block align-center justify-center"
                            href="/for-participants">Подробнее</a>
                    </div>
                </div>
                <div class="col-6 card">
                    <div class="text flex-block align-center justify-center">
                        <div class="information">
                            <img src="/wp-content/themes/lot/assets/images/ticket-blue.svg" alt="">
                            <div class="block">
                                <h2>ДЛЯ УЧАСТНИКОВ</h2>
                                <p>Игрок — рациональный индивид, который принимает решения, сравнивая возможные
                                    выгоды и
                                    издержки, стремясь увеличить своё благосостояние</p>
                            </div>
                        </div>
                        <a class="button blue flex-block align-center justify-center"
                            href="/for-participants">Подробнее</a>
                    </div>
                </div>
                <img src="/wp-content/themes/lot/assets/images/balls-gray.svg" class="background">
            </div>
        </div>
    </section>
    <section class="third-slide blue">
        <div class="container theory">
            <div class="row">
                <div class="col-6">
                    <h2>ПРИКЛАДНАЯ МАТЕМАТИКА.</h2>
                    <p class="subheader">Теории игр, случайных процессов и вероятностей</p>
                    <p class="text">«О расчётах при азартных играх» — трактат 1657 года. Исследуя прогнозирование
                        выигрыша в
                        азартных играх, Блез Паскаль, Пьер Ферма и Христиан Гюйгенс открыли первые вероятностные
                        закономерности, возникающие при бросании костей. И ещё много интересного…</p>
                    <div class="block flex-block justify-between">
                        <a href="#">Подробнее</a>
                        <div class="buttons flex-block align-center justify-center">
                            <div class="left yellow">
                                <img src="/wp-content/themes/lot/assets/images/arrow-left.svg">
                            </div>

                            <div class="right yellow">
                                <img src="/wp-content/themes/lot/assets/images/arrow-right.svg">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <img src="/wp-content/themes/lot/assets/images/ball-yellow.svg" class="ball" alt="">
                </div>
                <img src="/wp-content/themes/lot/assets/images/ball-yellow-blur.svg" class="ball-blur">
            </div>
        </div>
    </section>
    <section class="fourth-slide">
        <div class="container stocks">
            <div class="row">
                <div class="col-6">
                    <img class="image" src="/wp-content/themes/lot/assets/images/image-with-big-b.png" alt="">
                </div>
                <div class="col-6">
                    <div class="block">
                        <h2>СТОИМОСТЬ ПОДПИСКИ,<br> АКЦИИ, ПАРТНЁРСКАЯ<br> ПРОГРАММА</h2>
                        <p>Акция! Скидка 40% на тариф «Участник + Игрок».Это самая полная, фундаментальная
                            информация
                            для
                            вас. Предусмотрена возможность бесплатного доступа для участников партнёрской программы
                        </p>
                        <div class="button yellow">Подробнее</div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="tables">
        <div class="container">
            <div class="table-1" data-name="Игрокам события в линии">
                <h2>
                    ИГРОКАМ СОБЫТИЯ В ЛИНИИ
                </h2>
                <table class="table"  style="width:100%">
                    <thead>
                        <tr>
                            <th>Лотерея</th>
                            <th>Тираж №</th>
                            <th>6 до MAX</th>
                            <th>5 до MAX</th>
                            <th>4 до MAX</th>
                            <th>3 до MAX</th>
                            <th>2 до MAX</th>
                            <th>1 до MAX</th>
                            <th>MAX</th>
                        </tr>
                    </thead>
                    <?php
                    $tables = [
                        'Спортлото 6 из 45','Спортлото 5 из 36','Спортлото 4 из 20','Рапидо', 'Радидо 2.0', 
                        'Дуэль','ТОП-3', 'КЕНО', '6 из 36','Рокетбинго','Спортлото 7 из 49'
                    ];
                    ?>
                    <tbody>
                        <?php foreach ($tables as $index => $row_name){?>
                            <tr>
                                <th>
                                   <?=$row_name?> 
                                </th>
                                <td>0</td>
                                <td>0</td>
                                <td>0</td>
                                <td>0</td>
                                <td>0</td>
                                <td>0</td>
                                <td>0</td>
                                <td>0</td>
                            </tr>
                        <?php }?>
                    <tbody>
                </table>
            </div>
            <div class="table-2" data-name="Участникам 'Национальной лотереи'">
                <h2>
                    Участникам "Национальной лотереи"
                </h2>
                <table class="table"  style="width:100%">
                    <thead>
                        <tr>
                            <th>Лотерея</th>
                            <th>Тираж №</th>
                            <th>6 до MAX</th>
                            <th>5 до MAX</th>
                            <th>4 до MAX</th>
                            <th>3 до MAX</th>
                            <th>2 до MAX</th>
                            <th>1 до MAX</th>
                            <th>MAX</th>
                        </tr>
                    </thead>
                    <?php
                    $tables = [
                        'Мечталион','Форсаж 75','Пятая Скорость', '5 из 37','Трижды три', 'Великолепная 8','5 из 50','Лавина Призов'
                    ];
                    ?>
                    <tbody>
                        <?php foreach ($tables as $index => $row_name){?>
                            <tr>
                                <th>
                                   <?=$row_name?> 
                                </th>
                                <td>0</td>
                                <td>0</td>
                                <td>0</td>
                                <td>0</td>
                                <td>0</td>
                                <td>0</td>
                                <td>0</td>
                                <td>0</td>
                            </tr>
                        <?php }?>
                    <tbody>
                </table>
            </div>
            <div class="table-3" data-name="Участникам 'Столото'">
                <h2>
                    Участникам "Столото"
                </h2>
                <table class="table"  style="width:100%">
                    <thead>
                        <tr>
                            <th>Лотерея</th>
                            <th>Тираж №</th>
                            <th>6 до MAX</th>
                            <th>5 до MAX</th>
                            <th>4 до MAX</th>
                            <th>3 до MAX</th>
                            <th>2 до MAX</th>
                            <th>1 до MAX</th>
                            <th>MAX</th>
                        </tr>
                    </thead>
                    <?php
                    $tables = [
                        'Бинго 75','Спортлото 6 из 45','Спортлото Матчбол', 'Спортлото 5 из 36', 'Спортлото 4 из 20','Зодиак', 
                        'Рапидо', 'Рапидо 2.0', '12/24', 'Дуэль', 'Джокер', 'ТОП-3', 'КЕНО','Жилищная лотерея','6 из 36', 
                        'Русское лото', 'Рокетбинго','Золотая подкова', 'Спортлото 7 из 49'
                    ];
                    ?>
                    <tbody>
                        <?php foreach ($tables as $index => $row_name){?>
                            <tr>
                                <th>
                                   <?=$row_name?> 
                                </th>
                                <td>0</td>
                                <td>0</td>
                                <td>0</td>
                                <td>0</td>
                                <td>0</td>
                                <td>0</td>
                                <td>0</td>
                                <td>0</td>
                            </tr>
                        <?php }?>
                    <tbody>
                </table>
            </div>
        </div>
    </section>
    <section class="faq-slide">
        <div class="container">
            <h2>ЧАСТО ЗАДАВАЕМЫЕ ВОПРОСЫ</h2>
            <div class="faq-list">
                <div class="faq open">
                    <div class="question flex-block align-center justify-between">
                        <h3>КАКИЕ ВАРИАНТЫ ОПЛАТЫ?</h3>
                        <img class="question-button" src="/wp-content/themes/lot/assets/images/minus-yellow.svg">
                    </div>
                    <hr>
                    <div class="answer">
                        <p><strong>Ответ</strong>: Стратегия является тем сценарием вашей игры, при котором вы
                            предполагаете её
                            наибольший успех. Она должна включать в себя финансовый менеджмент и критерии, по
                            которым
                            производится отбор исходов.</p>
                    </div>
                </div>
                <div class="faq">
                    <div class="question flex-block align-center justify-between">
                        <h3>КАК ПОЛУЧИТЬ ВЫИГРЫШ?</h3>
                        <img class="question-button" src="/wp-content/themes/lot/assets/images/plus-gray.svg">
                    </div>
                    <hr>
                    <div class="answer">
                        <p><strong>Ответ</strong>: Стратегия является тем сценарием вашей игры, при котором вы
                            предполагаете её
                            наибольший успех. Она должна включать в себя финансовый менеджмент и критерии, по
                            которым
                            производится отбор исходов.</p>
                    </div>
                </div>
                <div class="faq">
                    <div class="question flex-block align-center justify-between">
                        <h3>КАКИЕ БЫВАЮТ ЛОТЕРЕИ?</h3>
                        <img class="question-button" src="/wp-content/themes/lot/assets/images/plus-gray.svg">
                    </div>
                    <hr>
                    <div class="answer">
                        <p><strong>Ответ</strong>: Стратегия является тем сценарием вашей игры, при котором вы
                            предполагаете её
                            наибольший успех. Она должна включать в себя финансовый менеджмент и критерии, по
                            которым
                            производится отбор исходов.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="attention">
        <div class="container">
            <div class="wrapper flex-block align-center justify-between">
                <h2 class="header">
                    НИ В КОЕМ СЛУЧАЕ НЕ ИГРАЙТЕ НА ПОСЛЕДНИЕ ДЕНЬГИ В СЕМЬЕ<br>
                    B ТЕМ БОЛЕЕ НЕ ДЕЛАЙТЕ СТАВКИ НА ДЕНЬГИ, ВЗЯТЫЕ В ДОЛГ!
                </h2>
                <h2 class="sign">!</h2>
            </div>
            <img src="/wp-content/themes/lot/assets/images/balls-blue-blur.svg" class="background">
            <img src="/wp-content/themes/lot/assets/images/triangles-blur.svg" class="right-corner">
            <img src="/wp-content/themes/lot/assets/images/triangles-blur.svg" class="left-corner">
        </div>
    </section>
</main>

<?php get_footer(); ?>