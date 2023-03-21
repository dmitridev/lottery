<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ЛОТЕРЕЯ</title>
    <?php wp_head(); ?>
</head>
<body>
    <header class="main">
        <div class="container">
            <div class="flex-block">
                <div class="menu-wrapper">
                    <a href="/">
                        <img src="/wp-content/themes/lot/assets/images/logo.svg" alt="Главная">
                    </a>
                    <div class="burger-button">
                        <img src="/wp-content/themes/lot/assets/images/plus-gray.svg">
                    </div>
                </div>
				<?php 
					$is_user_logged_in = is_user_logged_in();
				?>
                <ul class="links <?php if($is_user_logged_in) echo 'with-user'; ?>">
                    <li><a href="/">ГЛАВНАЯ</a></li>
                    <li><a href="/lotteries">ЛОТЕРЕИ</a></li>
                </ul>
				<?php if($is_user_logged_in){
					$user = wp_get_current_user();
				?>
					<div class="buttons">
						<a class="login"><?=$user->display_name?></a>
						<a href="/account" class="registration account yellow">Личный кабинет</a>
					</div>
				<?php }else {?>
                <div class="buttons">
                    <a href="/login" class="login">Войти</a>
                    <a href="/register" class="registration yellow">Регистрация</a>
                </div>
				<?php }?>
                <div class="menu">
                    <ul>
                        <li><a href="/">ГЛАВНАЯ</a></li>
                        <li><a href="/for-participants">ЛОТЕРЕИ</a></li>
                        <li><a href="/contacts">КОНТАКТЫ</a></li>
                        <li><a href="/login" class="login">ВОЙТИ</a></li>
                        <li><a href="/registration" class="registration">РЕГИСТРАЦИЯ</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </header>