<script setup>
import { Link } from "@inertiajs/vue3";
import { computed } from "vue";

const props = defineProps({
	listing: Object,
});

const imageUrl = computed(() => {
	return props.listing.image
		? `/storage/${props.listing.image}`
		: "/storage/images/listing/default.jpg";
});
</script>

<template>
	<div
		class="bg-white rounded-lg shadow-lg overflow-hidden dark:bg-slate-800 h-96 flex flex-col justify-between relative"
	>
		<div>
			<!-- Image -->
			<Link :href="route('listing.show', listing.id)">
				<img
					:src="imageUrl"
					class="w-full h-72 object-cover object-center bg-slate-300"
					alt=""
				/>
			</Link>

			<!-- Name & user -->
			<div class="p-2">
				<h3 class="font-bold text-lg mb-1">{{ listing.name.substring(0, 40) }}</h3>
				<h3 class="font-bold text-sm text-blue-500 mb-1">
					{{ listing.species.substring(0, 40) }}
				</h3>

				<p class="text-sm">
					Listed on
					{{ new Date(listing.created_at).toLocaleDateString() }}
				</p>
			</div>
		</div>

		<p
			v-if="listing.status === 'unavailable'"
			class="absolute top-2 left-2 bg-red-500 text-white px-2 py-1 rounded-md font-bold"
		>
			Adopted
		</p>
	</div>
</template>

<style scoped>
/* Add any additional styles here */
</style>
