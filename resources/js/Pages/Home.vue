<script setup>
import { Head } from "@inertiajs/vue3";
import { useForm } from "@inertiajs/vue3";
import InputField from "../Components/InputField.vue";
import Card from "../Components/Card.vue";
import HowItWorks from "../Components/HowItWorks.vue";
import Footer from "../Components/Footer.vue";
import NavLink from "../Components/NavLink.vue";

const props = defineProps({
	listings: Object,
	adoptionApplications: Object,
});

const form = useForm({
	search: "",
});

const search = () => {
	form.get(route("home"), { preserveScroll: true });
};
</script>

<template>
	<Head title="Home" />
	<div
		class="relative bg-cover bg-center h-screen"
		style="background-image: url('/storage/images/bg1.png')"
	>
		<div class="absolute inset-0 bg-gradient-to-r from-black/50 to-transparent"></div>
		<div
			class="relative z-10 flex flex-col items-start p-9 justify-center h-full max-w-7xl mx-auto"
		>
			<h2 class="text-5xl font-bold mb-6 text-white">EXPLORE OUR RESCUES!</h2>
			<p class="text-xl text-white/90 max-w-2xl mb-8 leading-relaxed">
				Discover a wide range of rescues available for adoption. Whether you're looking
				for a playful puppy, a cuddly kitten, or a loyal companion, find one that you
				think best suits you.
			</p>
			<div class="space-x-4">
				<NavLink
					:href="route('explore')"
					class="bg-slate-900 text-white px-8 py-3 rounded-full hover:bg-blue-700 transition-all"
				>
					Adopt a Pet
				</NavLink>
			</div>
		</div>
	</div>
	<br />
	<br />
	<!-- How It Works -->
	<HowItWorks />
	<br />
	<br />
	<div class="bg-gray-50 py-16">
		<div class="max-w-7xl mx-auto px-4">
			<div class="flex justify-between items-center mb-8">
				<h2 class="text-3xl font-bold">Latest Rescues</h2>
				<div class="w-1/3">
					<form @submit.prevent="search" class="flex gap-2">
						<InputField
							type="search"
							label=""
							icon="magnifying-glass"
							placeholder="Search pets..."
							v-model="form.search"
							class="flex-1"
						/>
						<button
							type="submit"
							class="bg-blue-600 text-white px-4 rounded-lg hover:bg-blue-700"
						>
							Search
						</button>
					</form>
				</div>
			</div>

			<!-- listings -->
			<div v-if="Object.keys(listings.data).length">
				<div class="carousel-container">
					<div v-for="listing in listings.data" :key="listing.id" class="carousel-item">
						<Card :listing="listing" />
					</div>
				</div>
			</div>

			<div v-else class="text-center py-12 text-gray-600">
				No pets found matching your search criteria
			</div>
		</div>
	</div>

	<!-- Donations and Support Section -->
	<div class="bg-white py-16">
		<div class="max-w-7xl mx-auto px-4 text-center">
			<h2 class="text-3xl font-bold mb-4">Donate to Save Lives!</h2>
			<p class="text-gray-700 mb-6">
				Your donations help provide medical care, food, and shelter for pets in need.
				Reach us through email to donate.
			</p>
			<a href="#" class="underline px-6 py-3 hover:text-blue-700 transition">
				pawfectcompanion@gmail.com
			</a>
		</div>
	</div>

	<!-- About Us Section -->
	<div class="bg-gray-100 py-16">
		<div class="max-w-7xl mx-auto px-4">
			<h2 class="text-3xl font-bold mb-8 text-center text-gray-800">About Us</h2>
			<div class="grid grid-cols-1 md:grid-cols-3 gap-8">
				<!-- Mission Card -->
				<div
					class="bg-white p-8 rounded-xl shadow-md hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300 border-t-4 border-blue-500"
				>
					<div class="text-blue-500 mb-4">
						<i class="fas fa-paw fa-3x"></i>
					</div>
					<h3 class="text-2xl font-semibold mb-4 text-gray-800">Our Mission</h3>
					<p class="text-gray-600 leading-relaxed">
						At PawfectCompanion, we are dedicated to rescuing, rehabilitating, and
						rehoming pets who have faced challenging circumstances. Our mission is to
						provide a second chance for animals in need, ensuring they receive the care,
						love, and opportunities they deserve. Since our inception, we've successfully
						helped over 500 pets find their forever homes.
					</p>
				</div>

				<!-- Impact Card -->
				<div
					class="bg-white p-8 rounded-xl shadow-md hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300 border-t-4 border-red-500"
				>
					<div class="text-red-500 mb-4">
						<i class="fas fa-heart fa-3x"></i>
					</div>
					<h3 class="text-2xl font-semibold mb-4 text-gray-800">Our Impact</h3>
					<p class="text-gray-600 leading-relaxed">
						Each rescue represents a story of hope and resilience, and we are committed to
						being a bridge between vulnerable animals and compassionate families ready to
						welcome them. Through our extensive network of volunteers, foster homes, and
						partners, we not only rescue animals but also provide medical care, behavioral
						support, and the nurturing environment necessary to prepare them for adoption.
					</p>
				</div>

				<!-- Vision Card -->
				<div
					class="bg-white p-8 rounded-xl shadow-md hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300 border-t-4 border-yellow-500"
				>
					<div class="text-yellow-500 mb-4">
						<i class="fas fa-star fa-3x"></i>
					</div>
					<h3 class="text-2xl font-semibold mb-4 text-gray-800">Our Vision</h3>
					<p class="text-gray-600 leading-relaxed">
						Our efforts are guided by a deep belief in the transformative power of love
						and companionship, which every pet brings into their adoptive family. Join us
						in making a difference—adopt, volunteer, or support our mission today, and be
						a part of giving every rescued pet the happy ending they deserve.
					</p>
				</div>
			</div>
		</div>
	</div>
	<!-- Footer -->
	<Footer />
</template>

<style scoped>
.bg-cover {
	background-size: cover;
}
.bg-center {
	background-position: center;
}
.carousel-container {
	overflow-x: scroll;
	display: flex;
	gap: 1rem;
	scroll-snap-type: x mandatory;
}

.carousel-item {
	flex: 0 0 auto;
	scroll-snap-align: start;
	width: calc(100% / 3 - 1rem);
}
</style>
