<script setup lang="ts">
const currentYear = new Date().getFullYear();
</script>

<template>
	<footer class="bg-gray-900 text-white mt-16">
		<div class="max-w-7xl mx-auto px-4 py-9 grid grid-cols-1 md:grid-cols-4 gap-8">
			<div class="flex items-center space-x-4 ml-12">
				<NavLink routeName="home">
					<img src="/storage/app/public/images/listing/logopaw.svg" class="w-52" />
					<p>PawfectCompanion</p>
					<div>
						<br />
						<h4 class="text-md font-semibold">Follow Us</h4>
						<div class="flex space-x-4 text-gray-400">
							<a href="#" class="hover:text-white">
								<i class="fa-brands fa-facebook-f fa-lg"></i>
							</a>
							<a href="#" class="hover:text-white">
								<i class="fa-brands fa-twitter fa-lg"></i>
							</a>
							<a href="#" class="hover:text-white">
								<i class="fa-brands fa-instagram fa-lg"></i>
							</a>
						</div>
					</div>
				</NavLink>
			</div>
			<div></div>
			<div>
				<h4 class="text-lg font-semibold mb-4">Quick Links</h4>
				<ul class="space-y-2">
					<li><a href="/" class="text-gray-400 hover:text-white">home</a></li>
					<li>
						<a href="/explore" class="text-gray-400 hover:text-white">Available Pets</a>
					</li>
					<li><a href="/status" class="text-gray-400 hover:text-white">Requests</a></li>

					<li>
						<a href="/profile" class="text-gray-400 hover:text-white">profile</a>
					</li>
				</ul>
			</div>
			<div>
				<h4 class="text-lg font-semibold mb-4">Contact Us</h4>
				<ul class="space-y-2 text-gray-400">
					<li>pawfectcompanion@gmail.com</li>
					<li>Phone: (+63) 968-536-4437</li>
					<li>www.fb.com/pawfectcompanion</li>
				</ul>
			</div>
		</div>
		<div class="border-t border-gray-800">
			<div class="max-w-7xl mx-auto px-4 py-6 text-center text-gray-400">
				<p>&copy; {{ currentYear }} PawfectCompanion. All rights reserved.</p>
			</div>
		</div>
	</footer>
</template>
