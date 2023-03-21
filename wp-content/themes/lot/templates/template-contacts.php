<?php 
    /*
        Template Name: Контакты
    */
    get_header();
?>
 <main class="contacts">
        <section class="header">
            <div class="container">
                <div class="breadcrumbs">
                    <a href="/">Главная</a> / <a href="#">Контакты</a>
                </div>
                <h1>КОНТАКТЫ</h1>
            </div>
        </section>

        <section class="yandex-maps">
            <div class="container">
                <iframe
                    src="https://yandex.com/map-widget/v1/?um=constructor%3A510a3019d21a29a7d413a768350f6b9d3c45204932e5bfc91e991b23a1cbd0ab&amp;source=constructor"
                    frameborder="0"></iframe>
            </div>
        </section>

        <section class="contacts-information">
            <div class="container">
                <div class="row flex-block">
                    <div class="col-6 card flex-block">
                        <img src="/wp-content/themes/lot/assets/images/map.svg">
                        <div class="text">
                            <h3>АДРЕС</h3>
                            <p>12345 North Main Street<br>
                                New York, NY 555555
                            </p>
                        </div>
                    </div>
                    <div class="col-6 card flex-block">
                        <img src="/wp-content/themes/lot/assets/images/contact.svg" class="contacts-image">
                        <div class="text">
                            <h3>КОНТАКТЫ</h3>
                            <p>
                                <strong>Телефон</strong> 1.888.555.6789<br>
                                info@your-domain.com
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section class="have-questions">
            <div class="container">
                <h2>ОСТАЛИСЬ ВОПРОСЫ?</h2>
                <form class="questions-form card">
                    <input placeholder="ВАШЕ ФИО">
                    <input placeholder="ВАШ ТЕЛЕФОН">
                    <input placeholder="ДОПОЛНИТЕЛЬНАЯ ИНФОРМАЦИЯ">
                    <div class="block flex-block justify-between align-center">
                        <p class="conditions">
                            <strong>Обработка персональных данных:</strong> Отправляя сообщение вы соглашаетесь с
                            условиями<br> обработки персональных данных.
                        </p>
                        <div class="button yellow">Написать</div>
                    </div>
                </form>
            </div>
        </section>
	 
	 <?php get_template_part("template-parts/footer-banner");?>
    </main>
<?php 
    get_footer();
?>