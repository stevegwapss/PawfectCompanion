<script setup>
import { computed } from "vue";

const props = defineProps({
	listings: Object,
	adoptionApplications: Object,
});

// Computed metrics
const metrics = computed(() => {
	if (!props.listings?.data || !props.adoptionApplications) return null;

	const listingsData = props.listings.data;
	const totalListings = listingsData.length;

	// Calculate metrics
	const availableListings = listingsData.filter((l) => l.status === "available").length;
	const pendingApplications = props.adoptionApplications.filter(
		(a) => a.status === "pending"
	).length;
	const adoptedListings = listingsData.filter((l) => l.status === "unavailable").length;

	// Calculate adoption rate (adopted / total)
	const adoptionRate = totalListings ? (adoptedListings / totalListings) * 100 : 0;

	return {
		totalListings,
		availableListings,
		pendingApplications,
		adoptedListings,
		adoptionRate: adoptionRate.toFixed(1),
	};
});
</script>

<template>
	<div class="bg-slate-900 text-white py-16">
		<div class="max-w-7xl mx-auto px-4">
			<div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-center">
				<div>
					<p class="text-4xl font-bold mb-2">{{ metrics?.totalListings }}</p>
					<p class="text-blue-100">Total Listings</p>
				</div>
				<div>
					<p class="text-4xl font-bold mb-2">{{ metrics?.adoptedListings }}</p>
					<p class="text-blue-100">Pets Adopted</p>
				</div>
				<div>
					<p class="text-4xl font-bold mb-2">{{ metrics?.adoptionRate }}%</p>
					<p class="text-blue-100">Adoption Rate</p>
				</div>
			</div>
		</div>
	</div>
</template>
