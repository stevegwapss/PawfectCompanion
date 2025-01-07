<template>
	<Head :title="listing.name" />

	<Container class="flex gap-4">
		<div class="w-80 rounded-md overflow-hidden">
			<img
				:src="
					listing.image
						? `/storage/${listing.image}`
						: '/storage/images/listing/default.jpg'
				"
				class="w-full h-full object-cover object-center"
				alt=""
			/>
		</div>

		<div class="w-3/4">
			<!-- Listing info -->
			<div class="mb-6">
				<div class="flex items-end justify-between mb-2">
					<p class="text-slate-400 w-full border-b">Pet Details</p>

					<!-- Adopt-button -->
					<div
						v-if="user.role !== 'admin' && listing.status !== 'unavailable'"
						class="pl-4 flex items-center gap-4"
					>
						<Link
							:href="route('adoption.form', { listingId: listing.id })"
							class="bg-green-500 rounded-md text-white px-6 py-2 hover:outline outline-green-500 outline-offset-2"
						>
							Adopt
						</Link>
					</div>
					<p v-else class="text-red-500 font-bold">Adopted</p>
				</div>

				<h3 class="font-bold text-2xl mb-4">{{ listing.name }}</h3>
				<p>{{ listing.desc }}</p>
				<br />
				<p v-if="listing.species">Species: {{ listing.species }}</p>
				<p v-if="listing.breed">Breed: {{ listing.breed }}</p>
				<p v-if="listing.color">Color: {{ listing.color }}</p>
			</div>

			<!-- Contact info -->
			<div class="mb-6">
				<p class="text-slate-400 w-full border-b mb-2">Contact Info</p>

				<div>
					<p>Email: PawfectCompanion@gmail.com</p>
					<p>Contact #: 0123456789</p>
					<br />
					<p>
						Listed on
						{{ new Date(listing.created_at).toLocaleDateString() }}
					</p>
				</div>
			</div>
		</div>
	</Container>
</template>

<script setup>
import { router } from "@inertiajs/vue3";
import Container from "../../Components/Container.vue";

const props = defineProps({
	listing: Object,
	user: Object,
	canModify: Boolean,
});

const deleteListing = () => {
	if (confirm("Are you sure?")) {
		router.delete(route("listing.destroy", props.listing.id));
	}
};
</script>
