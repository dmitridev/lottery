<?php 
    /*
        Template Name: Логин
    */
    get_header();
?>

 <main class="login">

        <section class="header">
            <div class="container">
                <div class="breadcrumbs">
                    <a href="/">Главная</a> / <a href="#">Войти в личный кабинет</a>
                </div>
                <h1>ЛИЧНЫЙ КАБИНЕТ</h1>
            </div>
        </section>

        <section class="login-section">
            <div class="container">
                <form class="login-form">
                    <h2>
                        ВОЙТИ В ЛИЧНЫЙ КАБИНЕТ
                    </h2>
                    <div class="row">
                        <div class="form-column">
                            <input placeholder="Имя" class="blue" name="first_name" required>
                            <input placeholder="Фамилия" class="blue" name="last_name" required>
                            <input placeholder="Отечество" class="blue" name="second_name" required>
                            <input placeholder="Email" class="blue" name="email" required>

                            <div class="buttons">
                                <div class="col-6 form-button registration">
                                    <a class="button registration yellow" href="/register">Регистрация</a>
                                </div>
                                <div class="col-6 form-button login">
                                    <button type="submit" class="button login blue">Авторизация</button>
                                </div>
                            </div>
							<p class="login-complete">
								
							</p>
                        </div>

                    </div>
                </form>
            </div>
        </section>

        <?php get_template_part('template-parts/footer-banner');?>
    </main>


<?php get_footer();?>