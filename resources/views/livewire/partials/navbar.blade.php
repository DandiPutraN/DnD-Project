<div>
	<!-- Header -->
	<header class="header">
		<a href="#home"><img src="{{ asset('front/images/Logo.png') }}" class="logo" /></a>
	
		<i class="bx bx-menu" id="menu-icon"></i>
	
		<nav class="navbar">
			<a href="#home" class="active">Home</a>
			<a href="#about">About</a>
			<a href="#portfolio">Portfolio</a>
			<a href="#contact">Contact</a>
			<a href="/admin">Apps</a>
			{{-- <div style="position: relative;">
			  <a href="#" style="cursor: pointer;">Apps ▼</a>
			  <div style="display: none; position: absolute; top: 100%; left: 0; background: rgb(35, 34, 34); border: 1px solid #ccc; min-width: 160px;">
				<a href="/admin" style="display: block; padding: 10px; color: white; text-decoration: none;">Penjualan</a>
				<a href="/admin" style="display: block; padding: 10px; color: white; text-decoration: none;">Finance & Accounting</a>
			  </div>
			</div> --}}
		</nav>
		  
		  <script>
			// Buat simple toggle dropdown
			const dropdown = document.querySelector('.navbar div');
			const dropdownContent = dropdown.querySelector('div');
		  
			dropdown.addEventListener('mouseover', () => {
			  dropdownContent.style.display = 'block';
			});
			dropdown.addEventListener('mouseout', () => {
			  dropdownContent.style.display = 'none';
			});
		  </script>
		  
		</nav>
	
		<i class="bx bxs-moon" id="light-mode-icon"></i>
	</header>
</div>