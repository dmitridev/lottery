<?php 
    /*
        Template Name: Регистрация
    */
    get_header();
?>

   <main class="registration">

        <section class="header">
            <div class="container">
                <div class="breadcrumbs">
                    <a href="/">Главная</a> / <a href="#">Регистрация участника</a>
                </div>
                <h1>РЕГИСТРАЦИЯ УЧАСТНИКА</h1>
            </div>
        </section>

        <section class="registration-section">
            <div class="container">
                <form class="registration-form">
                    <h2>
                        СТАНЬТЕ НОВЫМ УЧАСТНИКОМ
                    </h2>
                    <div class="row">
                        <div class="col-6 form-column">
                            <input placeholder="Имя" class="blue" name="first_name" required>
							<p class="error" data-name="first_name">
								<!--вывод ошибки для поля-->
							</p>
                            <input placeholder="Фамилия" class="blue" name="last_name" required>
							<p class="error" data-name="last_name">
								<!--вывод ошибки для поля-->
							</p>
                            <input placeholder="Отечество" class="blue" name="second_name" required>
							<p class="error" data-name="second_name">
								<!--вывод ошибки для поля-->
							</p>
                            <input placeholder="Пол" class="blue" name="gender" required>
							<p class="error" data-name="gender">
								<!--вывод ошибки для поля-->
							</p>
                        </div>
                        <div class="col-6 form-column">
                            <input placeholder="Телефон" type="tel" class="blue" name="phone" required>
							<p class="error" data-name="phone">
								<!--вывод ошибки для поля-->
							</p>
                            <input placeholder="Email" class="blue" name="email" required>
							<p class="error" data-name="email">
								<!--вывод ошибки для поля-->
							</p>
                            <input placeholder="Дата рождения" class="blue" name="birth" required>
							<p class="error" data-name="birth">
								<!--вывод ошибки для поля-->
							</p>
                            <input placeholder="Кол-во лет участия в подобном" class="blue" name="years_of_playing" required>
							<p class="error" data-name="years_of_playing">
								<!--вывод ошибки для поля-->
							</p>
                        </div>
                    </div>
                    <div class="buttons">
                        <button type="submit" class="button registration yellow">Регистрация</button>
                        <a class="button login blue" href="/login">Авторизация</a>
                    </div>
					
					<p class="registration-complete">
					
					</p>
                </form>
            </div>
        </section>

        
        <?php get_template_part('template-parts/footer-banner');?>
    </main>

<?php 
    get_footer();
?>