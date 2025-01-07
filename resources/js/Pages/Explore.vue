<script setup>
import Card from "../Components/Card.vue";
import PaginationLinks from "../Components/PaginationLinks.vue";
import { useForm } from "@inertiajs/vue3";
import Footer from "../Components/Footer.vue";
import InputField from "../Components/InputField.vue";

const props = defineProps({
	listings: Object,
});

const form = useForm({
	search: "",
	status: "",
});

const search = () => {
	form.get(route("explore"), { preserveScroll: true });
};
</script>

<template>
	<br />
	<div class="flex justify-between items-center mb-8">
		<h2 class="text-3xl font-bold">Explore our Latest Rescues</h2>
		<form @submit.prevent="search" class="flex gap-2">
			<InputField
				v-model="form.search"
				icon="magnifying-glass"
				type="search"
				placeholder="Search pets..."
			/>
			<select v-model="form.status" class="px-4 py-2 rounded-md">
				<option value="">All</option>
				<option value="available">Available</option>
				<option value="unavailable">Unavailable</option>
			</select>
			<button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">
				Search
			</button>
		</form>
	</div>
	<br />
	<div v-if="Object.keys(listings.data).length">
		<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
			<div v-for="listing in listings.data" :key="listing.id">
				<Card :listing="listing" />
			</div>
		</div>
	</div>

	<div v-else class="text-center py-12 text-gray-600">
		No pets found matching your search criteria
	</div>

	<div class="mt-8">
		<PaginationLinks :paginator="listings" />
	</div>

	<!-- Footer -->
	<Footer />
</template>
