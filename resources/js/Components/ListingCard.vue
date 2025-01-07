<script setup>
import { defineProps } from "vue";

const props = defineProps({
	listing: Object,
});
</script>

<template>
	<div class="w-80 -ml-24 rounded-md overflow-hidden shadow-lg">
		<img
			:src="
				listing.image
					? `/storage/${listing.image}`
					: '/storage/images/listing/default.jpg'
			"
			class="w-full h-48 object-cover object-center"
			alt="Pet Image"
		/>
		<div class="p-4">
			<h3 class="font-bold text-xl mb-2">{{ listing.name }}</h3>
			<p>{{ listing.desc }}</p>
			<p v-if="listing.species">Species: {{ listing.species }}</p>
			<p v-if="listing.breed">Breed: {{ listing.breed }}</p>
			<p v-if="listing.color">Color: {{ listing.color }}</p>
			<p v-if="listing.status === 'unavailable'" class="text-red-500 font-bold">
				Adopted
			</p>
		</div>
	</div>
</template>
