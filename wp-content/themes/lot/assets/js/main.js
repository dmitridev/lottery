document.addEventListener("DOMContentLoaded", function () {
    Array.from(document.querySelectorAll(".faq .question-button")).forEach(button => {
        button.onclick = function (event) {
            const parent = this.parentNode.parentNode; // возвращаемся к .faq обьекту

            parent.classList.toggle('open')
            if (parent.classList.contains('open')) {
                this.src = "/wp-content/themes/lot/assets/images/minus-yellow.svg"
                return;
            }

            this.src = "/wp-content/themes/lot/assets/images/plus-gray.svg"
        }
    });
if(document.querySelector('.burger-button'))
    document.querySelector('.burger-button').onclick = function () {
		document.querySelector('.menu').classList.toggle('open');
        if (document.querySelector('.menu').classList.contains('open')) {
            this.querySelector('img').src = "/wp-content/themes/lot/assets/images/minus-yellow.svg";
            return;
        }
        this.querySelector('img').src = "/wp-content/themes/lot/assets/images/plus-gray.svg";
    }
	
	const printErrors = function(form,errors = {}){
		// теперь нужно найти поля которые с ошибками и вывести снизу них блок p
		if(Object.keys(errors).length > 0){
			Object.entries(errors).forEach(([key,value]) =>
				form.querySelector(`p.error[data-name=${key}]`).innerHTML=value['error']);
		}
	}
	
	const clearErrors = function(form){
		const names = [
			'first_name','second_name','last_name','gender','phone','email','birth'
		];
		
		names.forEach(name => form.querySelector(`p.error[data-name=${name}]`).innerHTML='')
	}
	
	const loginForm = document.querySelector('.login-form');
	if(loginForm)
	loginForm.onsubmit = async function(e){
		e.preventDefault();
		const first_name = this.elements.first_name.value;
		const second_name = this.elements.second_name.value;
		const last_name = this.elements.last_name.value;
		const email = this.elements.email.value;
		const formData = new FormData();
		formData.append('action','login');
		formData.append('first_name',first_name);
		formData.append('second_name',second_name);
		formData.append('last_name',last_name);
		formData.append('email',email);
		try{
			const request = await fetch('/wp-admin/admin-ajax.php',{
				method:'post',
				body:formData
			});
			
			const json = await request.json();
			if(json.success){
				const p = document.querySelector('p.login-complete');
				p.style="color:green";
				p.innerHTML = 'Вход выполнен';
				
				setTimeout(() => {
					p.innerHTML = '';
					window.location.href="/account";
				},2000)
			}
			
			if(json.error){
				const p = document.querySelector('p.login-complete');
				p.style="color:red";
				p.innerHTML = json.error;
				setTimeout(() => {
					p.innerHTML = '';
				},5000)
			}
			
		}catch(e){
			console.log(e);
		}
	}
	
	const registrationForm = document.querySelector('.registration-form');
	if(registrationForm)
	registrationForm.onsubmit = async function(e){
		e.preventDefault();
		clearErrors(this);
		const errors = {};
		const first_name = this.elements.first_name.value;
		const second_name = this.elements.second_name.value;
		const last_name = this.elements.last_name.value;
		const gender = this.elements.gender.value;
		const phone = this.elements.phone.value;
		const email = this.elements.email.value;
		const birth = this.elements.birth.value;
		const years_of_playing = this.elements.years_of_playing.value;
		// проверям длину имён.
		if(first_name.length < 3){
				errors['first_name'] = {
					'error' : 'Имя должно быть больше трёх символов',
					success:false,
				};
		}
		
		if(second_name.length < 2){
			errors['second_name'] = {
				'error':'Фамилия должна быть больше двух символов',
				success:false,
			}
		}
		
		if(last_name.length < 3){
			errors['last_name'] = {
				'error':'Отчество должно быть больше трёх символов',
				success:false
			}
		}
		
		if(phone.length < 11){
			errors['phone'] = {
				'error':'Телефон заполнен неверно',
				success:false
			}
		}
		
		if(birth.length < 10){
			errors['birth'] = {
				error:'Дата рождения заполнена неверно',
				success:false
			}
		}
		
		if(!email.includes('@')){
			errors['email'] = {
				error : 'Email некорректный',
				success: false
			}
		}
		
		if(Object.keys(errors).length != 0){
			printErrors(this,errors);
			return;
		}
			
		const formData = new FormData();
		formData.append('action','register')
		formData.append('first_name',first_name);
		formData.append('second_name',second_name);
		formData.append('last_name',last_name);
		formData.append('gender',gender);
		formData.append('phone',phone);
		formData.append('email',email);
		formData.append('birth',birth);
		formData.append('years_of_playing',years_of_playing);
		
		try{
			const request = await fetch('/wp-admin/admin-ajax.php',{
				method:'post',
				body:formData
			});
			
			const json = await request.json();
			console.log(json);
			if(json.success){
				const p = document.querySelector('p.registration-complete');
				p.style="color:green";
				p.innerHTML = 'Регистрация завершена';
				setTimeout(() => {
					p.innerHTML = '';
				},5000)
			}
			if(json.error){
				const p = document.querySelector('p.registration-complete');
				p.style="color:red";
				p.innerHTML = json.error;
				setTimeout(() => {
					p.innerHTML = '';
				},5000)
			}
			
		}catch(e){
			console.log(e);
		}
	}
	const accountForm = document.querySelector('form.account-form');
	if(accountForm)
	accountForm.onclick = async function(e){
		e.preventDefault();
		const formData = new FormData();
		
		const first_name = this.elements.first_name.value;
		const second_name = this.elements.second_name.value;
		const last_name = this.elements.last_name.value;
		const gender = this.elements.gender.value;
		const phone = this.elements.phone.value;
		const email = this.elements.email.value;
		const birth = this.elements.birth.value;
		const years_of_playing = this.elements.years_of_playing.value;
		const user_id = this.elements.user_id.value;
		formData.append('action','update-user');
		formData.append('user_id',user_id);
		formData.append('first_name',first_name);
		formData.append('second_name',second_name);
		formData.append('last_name',last_name);
		formData.append('gender',gender);
		formData.append('phone',phone);
		formData.append('email',email);
		formData.append('birth',birth);
		formData.append('years_of_playing',years_of_playing);
		
		const request = await fetch('/wp-admin/admin-ajax.php',{
			method:'post',
			body:formData
		});
		
		const json = await request.json();
		if(json.success){
				const p = document.querySelector('p.update-complete');
				p.style="color:green";
				p.innerHTML = 'Аккаунт обновлён';
				setTimeout(() => {
					p.innerHTML = '';
				},5000)
			}
			if(json.error){
				const p = document.querySelector('p.update-complete');
				p.style="color:red";
				p.innerHTML = json.error;
				setTimeout(() => {
					p.innerHTML = '';
				},5000)
			}
		
		
	}
	
	const button = document.querySelector('button.logout');
	if(button)
		button.addEventListener('click',async function(e){
		const formData = new FormData();
		formData.append('action','logout');
		try{
			const request = await fetch('/wp-admin/admin-ajax.php',{
				method:'post',
				body:formData,
			});
			window.location.reload();
		}catch(e){
			
		}	
	});
	
	const buttons = Array.from(document.querySelectorAll('.tariff__buttons button'));
	if(buttons)
	    buttons.forEach(button => button.onclick = function(e){
			buttons.forEach(e => {e.classList.remove('green'); e.parentNode.querySelector('p.info').innerHTML = '';}); // убираем если есть зелёные кнопки.
			this.classList.add('green');
			this.parentNode.querySelector('p.info').innerHTML = 'тариф выбран';
		});
	
	const getMore = document.querySelector('.get-more');
	if(getMore){
	getMore.onclick = async function(e){
		const formData = new FormData();
		formData.append('action','get_stoloto_top');
		
		const info = await fetch('/wp-admin/admin-ajax.php',{
			method:'post',
			body:formData,
		});
		
		const body = await info.json();
		const table = document.querySelector('.lots tbody');
		table.innerHTML = '';
		Object.values(body).forEach((item,index) => {
			let tr = document.createElement('tr');
			tr.innerHTML = `<td>Выпадет номер ${index+1}</td><td>${item.drop_out.max}</td><td>${item.drop_out.now}</td><td>${item.not_drop_out.max}</td><td>${item.not_drop_out.now}</td>`;
			table.appendChild(tr);
		});
		
		document.querySelector('p.update-complete').innerHTML = 'Данные обновлены';
		setTimeout(() =>{
			document.querySelector('p.update-complete').innerHTML = '';
		},1500);
		console.log(body);
	}
	getMore.click();
	}
});