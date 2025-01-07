<script setup>
import { Head, Link } from "@inertiajs/vue3";
import Container from "../Components/Container.vue";
import PaginationLinks from "../Components/PaginationLinks.vue";
import Footer from "../Components/Footer.vue";

const props = defineProps({
	adoptionRequests: Object,
});
</script>

<template>
	<Head title="Adoption Status" />

	<Container>
		<h1 class="text-2xl font-bold mb-6">My Adoption Requests</h1>

		<div class="text-center" v-if="adoptionRequests.data.length">
			<table class="min-w-full bg-white dark:bg-gray-800">
				<thead>
					<tr>
						<th class="py-2 px-4 border-b">Pet</th>
						<th class="py-2 px-4 border-b">Status</th>
						<th class="py-2 px-4 border-b">Actions</th>
					</tr>
				</thead>
				<tbody>
					<tr v-for="request in adoptionRequests.data" :key="request.id">
						<td class="py-2 px-4 border-b">{{ request.listing.name }}</td>
						<td class="py-2 px-4 border-b">
							<span v-if="request.status === 'approved'" class="text-green-500"
								>Approved</span
							>
							<span v-if="request.status === 'disapproved'" class="text-red-500"
								>Disapproved</span
							>
							<span v-if="request.status === 'pending'" class="text-yellow-500"
								>Pending</span
							>
						</td>
						<td class="py-2 px-4 border-b">
							<Link :href="route('adoption.overview', request.id)" class="text-indigo-500"
								>View</Link
							>
						</td>
					</tr>
				</tbody>
			</table>

			<div class="mt-6">
				<PaginationLinks :paginator="adoptionRequests" />
			</div>
		</div>
		<div v-else>No adoption requests found.</div>
	</Container>
	<Footer />
</template>
